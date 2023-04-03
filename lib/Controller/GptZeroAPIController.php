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

namespace OCA\GPTZero\Controller;

use OCP\AppFramework\Http;
use OCP\IConfig;
use OCP\IRequest;
use OCP\AppFramework\Controller;

use OCA\GPTZero\Service\GPTZeroAPIService;
use OCA\GPTZero\AppInfo\Application;
use OCP\AppFramework\Http\JSONResponse;

class GptZeroAPIController extends Controller {
	/** @var IConfig */
	private $config;

	/** @var GPTZeroAPIService */
	private $gptzeroAPIService;

	/** @var string|null */
	private $userId;

	/** @var string */
	private $apiToken;

	public function __construct(
		string $appName,
		IRequest $request,
		IConfig $config,
		GPTZeroAPIService $gptzeroAPIService,
		?string $userId
	) {
		parent::__construct($appName, $request);

		$this->config = $config;
		$this->gptzeroAPIService = $gptzeroAPIService;
		$this->userId = $userId;
		$this->apiToken = $this->config->getAppValue(Application::APP_ID, 'api_token');
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function postPredictText(string $text) {
		$result = $this->gptzeroAPIService->postPredictText($this->userId, $text);
		return new JSONResponse($result, Http::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function postPredictFiles(array $fileIds) {
		$result = $this->gptzeroAPIService->postPredictFiles($this->userId, $fileIds);
		return new JSONResponse($result, Http::STATUS_OK);
	}
}
