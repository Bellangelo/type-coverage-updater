<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageAutoUpdate\Commands;

use Bellangelo\TypeCoverageAutoUpdate\Factories\AnalyseProcessFactory;
use Bellangelo\TypeCoverageAutoUpdate\Factories\PHPStanConfigFactory;
use Bellangelo\TypeCoverageAutoUpdate\PHPStanOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('update');
        $this->setDescription('Update the type coverage in the neon file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Finding configuration...');

        $config = (new PHPStanConfigFactory())->create();
        $process = (new AnalyseProcessFactory())->create(__DIR__ . '/../../phpstan.neon');

        $process->run();

        $output = new PHPStanOutput($config, $process->getOutput());

        return Command::SUCCESS;
    }
}