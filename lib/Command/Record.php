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

namespace OCA\Talked\Command;

use OCP\IConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

class Record extends Command
{
    /** @var IConfig */
	private $config;

    /** @var LoggerInterface */
    private $logger;


	public function __construct(IConfig $config, LoggerInterface $logger) {
		parent::__construct();
		$this->config = $config;
        $this->logger = $logger;
	}

    protected function configure(): void
    {
        $this
            ->setName('talked:record')
            ->setDescription('Call recording for Nextcloud Talk')
            ->addArgument(
                'token',
                InputArgument::REQUIRED,
                'A Talk room token.'
            )
            ->addArgument(
                'cmd',
                InputArgument::OPTIONAL,
                'The command to run, the following are valid commands: info, status, start, stop and help.',
                'help'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $token = $input->getArgument('token');
        $cmd = $input->getArgument('cmd');
        $serverUrl = $this->config->getAppValue('talked', 'server_url', '');

        if ($serverUrl === '') {
            $output->writeln('A recording server hasn\'t been configured yet.');
            return 0;
        }

        if ($cmd === 'help' or $cmd === '') {
            $message = 'Talked - Call recording for Nextcloud Talk

You have the following options available:
        /recording start - Starts a call recording
        /recording stop - Stops the call recording
        /recording status - Checks if there is an active call recording
            ';

            $output->writeln($message);

            return 0;
        }

        if ($cmd === 'info') {
            $result = $this->sendGetRequest($serverUrl, '');

            $output->writeln($result);

            return 0;
        }

        if ($cmd === 'status') {
            $payload = [
                'token' => $token
            ];

            $result = $this->sendPostRequest($serverUrl, 'status', $payload);

            $output->writeln($result);

            return 0;
        }

        if ($cmd === 'start') {
            $payload = [
                'token' => $token
            ];

            $result = $this->sendPostRequest($serverUrl, 'start', $payload);

            $output->writeln($result);

            return 0;
        }

        if ($cmd === 'stop') {
            $payload = [
                'token' => $token
            ];

            $result = $this->sendPostRequest($serverUrl, 'stop', $payload);

            $output->writeln($result);

            return 0;
        }

        $output->writeln('The specified command doesn\'t exist.');
        return 0;
    }

    protected function sendGetRequest($serverUrl, $endpoint, $headers = []) {
        if ($this->config->getAppValue('talked', 'server_url', '0')) {
            $headers = $this->addBasicAuthHeaders($headers);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $serverUrl . '/' . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $result = curl_exec($ch);
        $curl_error_code = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error_code > 0) {
            $this->logger->error('cURL Error (' . $curl_error_code . '): ' . $curl_error);
            $message = 'An error occured while running the command. Please try again or contact an administrator.';
        } else {
            $message = json_decode($result)->message;
        }

        return $message;
    }

    protected function sendPostRequest($serverUrl, $endpoint, $payload,  $headers = []) {
        if ($this->config->getAppValue('talked', 'server_url', '0')) {
            $headers = $this->addBasicAuthHeaders($headers);
        }

        $headers[] = 'Content-Type: application/json';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $serverUrl . '/' . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $result = curl_exec($ch);
        $curl_error_code = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error_code > 0) {
            $this->logger->error('cURL Error (' . $curl_error_code . '): ' . $curl_error);
            $message = 'An error occured while running the command. Please try again or contact an administrator.';
        } else {
            $message = json_decode($result)->message;
        }

        return $message;
    }

    protected function addBasicAuthHeaders() {
        $username = $this->config->getAppValue('talked', 'http_basic_auth_username');
        $password = $this->config->getAppValue('talked', 'http_basic_auth_password');

        $base64EncodedAuth = base64_encode($username . ':' . $password);

        $headers[] = 'Authorization: Basic ' . $base64EncodedAuth;

        return $headers;
    }
}
