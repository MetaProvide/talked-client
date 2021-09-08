<?php

declare(strict_types=1);

/**
 * Talked - Call recording for Nextcloud Talk
 *
 * @copyright Copyright (C) 2021  Magnus Walbeck <mw@mwalbeck.org>
 *
 * @author Magnus Walbeck <mw@mwalbeck.org>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
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

namespace OCA\Talked\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\Settings\ISettings;

class Admin implements ISettings {

	/** @var string */
	public const APP_NAME = 'talked';

	/** @var IConfig */
	private $config;

	/**
	 * @param IConfig $config
	 */
	public function __construct(IConfig $config) {
		$this->config = $config;
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm(): TemplateResponse {
		$serverUrl = $this->config->getAppValue(self::APP_NAME, 'server_url', "");
		$useHttpBasicAuth = $this->config->getAppValue(self::APP_NAME, 'use_http_basic_auth', "0");
		$httpBasicAuthUsername = $this->config->getAppValue(self::APP_NAME, 'http_basic_auth_username', "");
		$httpBasicAuthPassword = $this->config->getAppValue(self::APP_NAME, 'http_basic_auth_password', "");
		$hideSettings = $this->config->getAppValue(self::APP_NAME, 'hide_settings', "0");
		return new TemplateResponse('talked', 'admin', [
			"serverUrl" => $serverUrl,
			"useHttpBasicAuth" => $useHttpBasicAuth,
			"httpBasicAuthUsername" => $httpBasicAuthUsername,
			"httpBasicAuthPassword" => $httpBasicAuthPassword,
			"hideSettings" => $hideSettings
		]);
	}

	/**
	 * @return string
	 */
	public function getSection(): string {
		return 'talk';
	}

	/**
	 * @return int
	 */
	public function getPriority(): int {
		return 50;
	}
}
