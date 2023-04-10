<?php
/**
 *
 * Nextcloud - GPTZero
 *
 * @copyright Copyright (c) 2023 Andrey Borysenko <andrey18106x@gmail.com>
 *
 * @copyright Copyright (c) 2023 Alexander Piskun <bigcat88@icloud.com>
 *
 * @author 2023 Andrey Borysenko <andrey18106x@gmail.com>
 *
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\GPTZero\Service;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use OCP\Files\IRootFolder;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IURLGenerator;
use Psr\Log\LoggerInterface;
use OCP\Http\Client\IClientService;
use OCP\Share\IManager as ShareManager;

use OCA\GPTZero\AppInfo\Application;
use OCP\ICacheFactory;
use OCP\ICache;


class GPTZeroMultiFormDataBuilder {
	private array $data = [];
	private string $boundary;

	public function __construct() {
		$this->boundary = $this->generateBoundary();
	}

	public function addData(string $name, string $value): self {
		$this->data[] = [$name, $value];
		return $this;
	}

	public function getContentType(): string {
		return 'multipart/form-data; boundary=' . $this->boundary;
	}

	public function build(): string {
		$eol = "\r\n";
		$data = "";

		foreach ($this->data as $item) {
			$key = $item[0];
			$value = $item[1];

			$data .= '--' . $this->boundary . $eol . 'Content-Disposition: form-data; name="files"; filename="' . $key . '"'
				. $eol . $eol . $value . $eol;
		}

		$data .= '--' . $this->boundary . '--' . $eol;
		return $data;
	}

	private function generateBoundary(): string {
		return md5(uniqid(time()));
	}
}

/**
 * Service to make requests to the GPTZero API
 */
class GPTZeroAPIService {
	const GPTZero_RESPONSE_CACHE_TTL = 3600;

	/** @var string */
	private $appName;

	/** @var LoggerInterface */
	private $logger;

	/** @var IL10N */
	private $l10n;

	/** @var \OCP\Http\Client\IClient */
	private $client;

	/** @var IConfig */
	private $config;

	/** @var IRootFolder */
	private $root;

	/** @var ShareManager */
	private $shareManager;

	/** @var IURLGenerator */
	private $urlGenerator;

	/** @var ICache */
	private $cache;

	/** @var string|null */
	private $apiToken;

	public function __construct(
		string $appName,
		LoggerInterface $logger,
		IL10N $l10n,
		IConfig $config,
		IRootFolder $root,
		ShareManager $shareManager,
		IURLGenerator $urlGenerator,
		IClientService $clientService,
		ICacheFactory $cacheFactory,
	) {
		$this->appName = $appName;
		$this->logger = $logger;
		$this->l10n = $l10n;
		$this->client = $clientService->newClient();
		$this->config = $config;
		$this->root = $root;
		$this->shareManager = $shareManager;
		$this->urlGenerator = $urlGenerator;
		$this->cache = $cacheFactory->createDistributed('reference');
		$this->apiToken = $this->config->getAppValue(Application::APP_ID, 'api_token');
	}

	public function postPredictText(string $userId, string $text) {
		// Check if the text is already processed through the API and cached
		$cacheKey = $this->getCacheKey($text);
		$cached = $this->cache->get($cacheKey);
		if ($cached) {
			return $cached;
		}
		$response = $this->request($userId, 'v2/predict/text', [
			'document' => $text,
		], 'POST');
		$corrected_documents = $this->postprocess_response($response);
		if (count($corrected_documents) > 0) {
			$this->cache->set($cacheKey, $corrected_documents, self::GPTZero_RESPONSE_CACHE_TTL);
		}
		return $corrected_documents;
	}

	public function postPredictFiles(string $userId, array $fileIds) {
		$multipart = new GPTZeroMultiFormDataBuilder();
		$userFolder = $this->root->getUserFolder($userId);
		foreach ($fileIds as $fileId) {
			$nodes = $userFolder->getById($fileId);
			if (count($nodes) === 1) {
				/** @var \OCP\Files\File */
				$file = $nodes[0];
				$multipart->addData($file->getName(), $file->getContent());
			}
		}
		$apiToken = $this->config->getAppValue(Application::APP_ID, 'api_token');
		try {
			$request = $multipart->build();
			$headers = [
				'Accept' => 'application/json',
				'Content-Type' => $multipart->getContentType(),
				'User-Agent' => Application::INTEGRATION_USER_AGENT,
				'X-Api-Key' => $apiToken,
			];
			$response = $this->client->post(Application::GPTZero_API_BASE_URL . '/' . 'v2/predict/files', [
				'body' => $request,
				'headers' => $headers,
			]);
			$body = $response->getBody();
			$respCode = $response->getStatusCode();

			if ($respCode >= 400) {
				return ['error' => $this->l10n->t('Bad credentials')];
			} else {
				$documents = json_decode($body, true);
				return $this->postprocess_response($documents);
			}
		} catch (ClientException $e) {
			$this->logger->debug('GPTZero API error : '.$e->getMessage(), ['app' => Application::APP_ID]);
			return ['error' => $e->getMessage()];
		}
	}

	private function getCacheKey(string $text) {
		return 'gptzero_' . md5($text);
	}

	/**
	 * @param string $userId
	 * @param string $endPoint
	 * @param array $params
	 * @param string $method
	 * @param bool $jsonResponse
	 * @return array|mixed|resource|string|string[]
	 * @throws Exception
	 */
	public function request(string $userId, string $endPoint, array $params = [], string $method = 'GET',
							bool $jsonResponse = true) {
		$apiToken = $this->config->getAppValue(Application::APP_ID, 'api_token');
		try {
			$url = Application::GPTZero_API_BASE_URL . '/' . $endPoint;
			$options = [
				'headers' => [
					'User-Agent' => Application::INTEGRATION_USER_AGENT,
					'X-Api-Key' => $apiToken
				],
			];

			if (count($params) > 0) {
				if ($method === 'GET') {
					$paramsContent = '';
					foreach ($params as $key => $value) {
						if (is_array($value)) {
							foreach ($value as $oneArrayValue) {
								$paramsContent .= $key . '[]=' . urlencode($oneArrayValue) . '&';
							}
							unset($params[$key]);
						}
					}
					$paramsContent .= http_build_query($params);
					$url .= '?' . $paramsContent;
				} else {
					$options['json'] = $params;
				}
			}

			if ($method === 'GET') {
				$response = $this->client->get($url, $options);
			} else if ($method === 'POST') {
				$response = $this->client->post($url, $options);
			} else if ($method === 'PUT') {
				$response = $this->client->put($url, $options);
			} else if ($method === 'DELETE') {
				$response = $this->client->delete($url, $options);
			} else {
				return ['error' => $this->l10n->t('Bad HTTP method')];
			}
			$body = $response->getBody();
			$respCode = $response->getStatusCode();

			if ($respCode >= 400) {
				return ['error' => $this->l10n->t('Bad credentials')];
			} else {
				if ($jsonResponse) {
					return json_decode($body, true);
				} else {
					return $body;
				}
			}
		} catch (ServerException | ClientException $e) {
			$this->logger->debug('GPTZero API error : '.$e->getMessage(), ['app' => Application::APP_ID]);
			return ['error' => $e->getMessage()];
		}
	}

	private function postprocess_response(array $response): array {
		if (!isset($response['documents']))
			return [];
		$documents = $response['documents'];
		$processed = [];
		foreach ($documents as $document) {
			$processed[] = $this->postprocess_document($document);
		}
		return $processed;
	}

	private function postprocess_document(array $response): array {
		$processed = [];
		$processed['average_generated_prob'] = number_format($response['average_generated_prob'] * 100, 2);
		$processed['completely_generated_prob'] = number_format($response['completely_generated_prob'] * 100, 2);
		$processed['overall_burstiness'] = number_format($response['overall_burstiness'], 2);
		$processed['paragraphs'] = $response['paragraphs'];
		$processed['sentences'] = $response['sentences'];
		return $processed;
	}
}
