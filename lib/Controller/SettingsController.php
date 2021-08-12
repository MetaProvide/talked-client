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

namespace OCA\Talked\Controller;

use OCP\AppFramework\Controller;
use OCP\IConfig;
use OCP\IRequest;

class SettingsController extends Controller
{

    /** @var string */
    public const APP_NAME = 'talked';

    /** @var IConfig */
    private $config;

    /**
     * @param IConfig $config
     * @param IRequest $request
     */
    public function __construct(
        IConfig $config,
        IRequest $request
    ) {
        parent::__construct(self::APP_NAME, $request);
        $this->config = $config;
    }

    /**
     * Set talked options
     */
    public function admin(): void
    {
        if ($this->request->getParam("server_url")) {
            $this->config->setAppValue(self::APP_NAME, "server_url", $this->request->getParam("server_url"));
        } else {
            $this->config->setAppValue(self::APP_NAME, "server_url", "");
        }

        if ($this->request->getParam("use_http_basic_auth")) {
            $this->config->setAppValue(self::APP_NAME, "use_http_basic_auth", "1");
        } else {
            $this->config->setAppValue(self::APP_NAME, "use_http_basic_auth", "0");
        }

        if ($this->request->getParam("http_basic_auth_username")) {
            $this->config->setAppValue(self::APP_NAME, "http_basic_auth_username", $this->request->getParam("http_basic_auth_username"));
        } else {
            $this->config->setAppValue(self::APP_NAME, "http_basic_auth_username", "");
        }

        if ($this->request->getParam("http_basic_auth_password")) {
            $this->config->setAppValue(self::APP_NAME, "http_basic_auth_password", $this->request->getParam("http_basic_auth_password"));
        } else {
            $this->config->setAppValue(self::APP_NAME, "http_basic_auth_password", "");
        }
    }
}
