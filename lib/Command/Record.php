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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Record extends Command
{
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
        $argument = $input->getArgument('argument');

        if ($argument === 'start') {
            $output->writeln('Setting up recorder...');
            return 0;
        }

        if ($argument === 'stop') {
            $output->writeln('Stopping recording...');
            return 0;
        }

        $output->writeln('Nothing to do.');
        return 0;
    }
}
