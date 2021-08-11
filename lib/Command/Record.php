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

class Record extends Command
{
    /** @var IConfig */
	private $config;

	public function __construct(IConfig $config) {
		parent::__construct();
		$this->config = $config;
	}

    protected function configure(): void
    {
        $this
            ->setName('talked:record')
            ->setDescription('Call recording for Nextcloud Talk')
            ->addArgument(
                'token',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'userId',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'argument',
                InputArgument::REQUIRED
            )
            ->setHelp('/record')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $token = $input->getArgument('token');
        $argument = $input->getArgument('argument');
        $talkedServer = $this->config->getAppValue('talked', 'talked_server', '');

        if ($talkedServer === '') {
            $output->writeln("A recording server hasn't been configured yet.");
            return 0;
        }

        if ($argument === 'info') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $talkedServer);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            $output->writeln($result);

            return 0;
        }

        if ($argument === 'status') {
            $payload = [
                'token' => $token
            ];

            $result = $this->sendPostRequest($talkedServer, 'status', $payload);

            $output->writeln($result);

            return 0;
        }

        if ($argument === 'start') {
            $payload = [
                'token' => $token
            ];

            $result = $this->sendPostRequest($talkedServer, 'start', $payload);

            $output->writeln($result);

            return 0;
        }

        if ($argument === 'stop') {
            $payload = [
                'token' => $token
            ];

            $result = $this->sendPostRequest($talkedServer, 'stop', $payload);

            $output->writeln($result);

            return 0;
        }

        $output->writeln('Nothing to do.');
        return 0;
    }

    protected function sendPostRequest($base_url, $endpoint, $payload,  $headers = []) {
        $headers[] = 'Content-Type: application/json';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $base_url . '/' . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
