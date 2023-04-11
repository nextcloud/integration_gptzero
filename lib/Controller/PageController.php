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

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\Util;

use OCA\GPTZero\AppInfo\Application;
use OCP\AppFramework\Services\IInitialState;
use OCP\IConfig;

class PageController extends Controller {
	/** @var IConfig */
	private $config;

	/** @var IInitialState */
	private $initialStateService;

	public function __construct(
		IRequest $request,
		IInitialState $initialStateService,
		IConfig $config
	) {
		parent::__construct(Application::APP_ID, $request);

		$this->config = $config;
		$this->initialStateService = $initialStateService;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * Render default index template
	 *
	 * @return TemplateResponse
	 */
	public function index(): TemplateResponse {
		Util::addScript(Application::APP_ID, Application::APP_ID . '-main');

		$completelyGeneratedProbMin = $this->config->getAppValue(Application::APP_ID, 'completely_generated_prob_min', 10.0);
		$completelyGeneratedProbMax = $this->config->getAppValue(Application::APP_ID, 'completely_generated_prob_max', 47.0);

		$this->initialStateService->provideInitialState('completely_generated_prob_config', [
			'min' => floatval($completelyGeneratedProbMin),
			'max' => floatval($completelyGeneratedProbMax),
		]);
		return new TemplateResponse(Application::APP_ID, 'main');
	}
}
