<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageAutoUpdate\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Update extends Command
{
    protected function configure(): void
    {
        $this->setName('update');
        $this->setDescription('Update the type coverage in the neon file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}