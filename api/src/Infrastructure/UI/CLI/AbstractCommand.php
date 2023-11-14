<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Start</info>');
    }

    protected function success(OutputInterface $output): int
    {
        $output->writeln('<info>Done!</info>');
        return self::SUCCESS;
    }
}