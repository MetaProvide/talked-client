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
use OCP\Util;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

class Record extends Command {
	/** @var IConfig */
	private $config;

	/** @var LoggerInterface */
	private $logger;


	public function __construct(IConfig $config, LoggerInterface $logger) {
		parent::__construct();
		$this->config = $config;
		$this->logger = $logger;
	}

	protected function configure(): void {
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

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$token = $input->getArgument('token');
		$arguments = explode(" ", $input->getArgument('cmd'));
		$serverUrl = $this->config->getAppValue('talked', 'server_url', '');

		if ($serverUrl === '') {
			$output->writeln('A recording server hasn\'t been configured yet.');
			return 0;
		}

		if ($arguments[0] === 'help' or $arguments[0] === '') {
			$message = 'Talked - Call recording for Nextcloud Talk

You have the following options available:
        /recording start - Starts a call recording
        /recording stop - Stops the call recording
        /recording status - Checks if there is an active call recording
        /recording help - Shows this help message
            ';

			$output->writeln($message);

			return 0;
		}

		if ($arguments[0] === 'info') {
			$result = $this->sendGetRequest($serverUrl, '');

			$output->writeln($result);

			return 0;
		}

		if ($arguments[0] === 'status') {
			$payload = [
				'token' => $token
			];

			$result = $this->sendPostRequest($serverUrl, 'status', $payload);

			$output->writeln($result);

			return 0;
		}

		if ($arguments[0] === 'start') {
			$payload = [
				'token' => $token,
				'nextcloud_version' => Util::getVersion()[0]
			];

			if (count($arguments) > 1) {
				$parsedArguments = $this->parseArguments($arguments);
				$payload = array_merge($parsedArguments, $payload);
			}

			$result = $this->sendPostRequest($serverUrl, 'start', $payload);

			$output->writeln($result);

			return 0;
		}

		if ($arguments[0] === 'stop') {
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

	protected function sendGetRequest(string $serverUrl, string $endpoint, array $headers = []): string {
		if ($this->config->getAppValue('talked', 'server_url', '0')) {
			$headers = $this->addBasicAuthHeaders($headers);
		}

		$curlHandle = curl_init();

		$curlHandle = $this->configureServerUri($curlHandle, $serverUrl, $endpoint);

		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
		curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 10);
		$result = curl_exec($curlHandle);
		$curlErrorCode = curl_errno($curlHandle);
		$curlError = curl_error($curlHandle);
		curl_close($curlHandle);

		if ($curlErrorCode > 0) {
			$this->logger->error('cURL Error (' . $curlErrorCode . '): ' . $curlError);
			$message = 'An error occured while running the command. Please try again or contact an administrator.';
		} else {
			$message = json_decode($result)->message;
		}

		return $message;
	}

	protected function sendPostRequest(string $serverUrl, string $endpoint, array $payload,  array $headers = []): string {
		if ($this->config->getAppValue('talked', 'server_url', '0')) {
			$headers = $this->addBasicAuthHeaders($headers);
		}

		$headers[] = 'Content-Type: application/json';

		$curlHandle = curl_init();

		$curlHandle = $this->configureServerUri($curlHandle, $serverUrl, $endpoint);

		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandle, CURLOPT_POST, true);
		curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
		curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 10);
		$result = curl_exec($curlHandle);
		$curlErrorCode = curl_errno($curlHandle);
		$curlError = curl_error($curlHandle);
		curl_close($curlHandle);

		if ($curlErrorCode > 0) {
			$this->logger->error('cURL Error (' . $curlErrorCode . '): ' . $curlError);
			$message = 'An error occured while running the command. Please try again or contact an administrator.';
		} else {
			$message = json_decode($result)->message;
		}

		return $message;
	}

	protected function addBasicAuthHeaders(): array {
		$username = $this->config->getAppValue('talked', 'http_basic_auth_username');
		$password = $this->config->getAppValue('talked', 'http_basic_auth_password');

		$base64EncodedAuth = base64_encode($username . ':' . $password);

		$headers[] = 'Authorization: Basic ' . $base64EncodedAuth;

		return $headers;
	}

	protected function configureServerUri($curlHandle, string $serverUrl, string $endpoint) {
		# Check if the URI is pointing to a unix socket
		if (substr($serverUrl, 0, 5) === 'unix:') {
			curl_setopt($curlHandle, CURLOPT_UNIX_SOCKET_PATH, substr($serverUrl, 5));
			# Configure the URL the requests should go to. In later versions
			# curl requires a dummy hostname, so here we just specify localhost.
			curl_setopt($curlHandle, CURLOPT_URL, 'http://localhost' . '/' . $endpoint);
		} else {
			# If the URI isn't pointing to a unix socket, assume we are connecting over TCP
			curl_setopt($curlHandle, CURLOPT_URL, $serverUrl . '/' . $endpoint);
		}
		return $curlHandle;
	}

	protected function parseArguments($arguments) {
		// Remove the initial command
		array_shift($arguments);

		$parsedArguments = [];

		foreach($arguments as $argument) {

			if (!str_contains($argument, '=')) {
				continue;
			}
			$parts = explode('=', $argument);

			switch ($parts[0]) {
				case 'audio_only':
					if (strtolower($parts[1]) === 'true') {
						$parsedArguments['audio_only'] = true;
					} elseif (strtolower($parts[1]) === 'false') {
						$parsedArguments['audio_only'] = false;
					}
					continue 2;
				default:
					continue 2;
			}
		}

		return $parsedArguments;
	}
}
