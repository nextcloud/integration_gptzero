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

namespace OCA\GPTZero\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\IConfig;
use OCP\Settings\ISettings;

use OCA\GPTZero\AppInfo\Application;

class Admin implements ISettings {
	/** @var IConfig */
	private $config;

	/** @var IInitialState */
	private $initialStateService;

	public function __construct(
		IConfig $config,
		IInitialState $initialStateService
	) {
		$this->config = $config;
		$this->initialStateService = $initialStateService;
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm(): TemplateResponse {
		$apiToken = $this->config->getAppValue(Application::APP_ID, 'api_token');
		$fileActionsMenu = $this->config->getAppValue(Application::APP_ID, 'file_actions_menu', '1');
		$completelyGeneratedProbMin = $this->config->getAppValue(Application::APP_ID, 'completely_generated_prob_min', 0.22);
		$completelyGeneratedProbMax = $this->config->getAppValue(Application::APP_ID, 'completely_generated_prob_max', 0.5);

		$adminConfig = [
			'api_token' => $apiToken,
			'file_actions_menu' => $fileActionsMenu === '1' ? true : false,
			'completely_generated_prob_min' => $completelyGeneratedProbMin,
			'completely_generated_prob_max' => $completelyGeneratedProbMax,
		];
		$this->initialStateService->provideInitialState('admin-config', $adminConfig);
		return new TemplateResponse(Application::APP_ID, 'adminSettings');
	}

	public function getSection(): string {
		return 'connected-accounts';
	}

	public function getPriority(): int {
		return 10;
	}
}
