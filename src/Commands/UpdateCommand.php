<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageUpdater\Commands;

use Override;
use Bellangelo\TypeCoverageUpdater\Factories\AnalyseProcessFactory;
use Bellangelo\TypeCoverageUpdater\Factories\PHPStanConfigFactory;
use Bellangelo\TypeCoverageUpdater\PHPStan\PHPStanOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class UpdateCommand extends Command
{
    #[Override]
    protected function configure(): void
    {
        $this->setName('update');
        $this->setDescription('Update the type coverage in the neon file.');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Finding configuration...');
        $phpStanConfigFactory = new PHPStanConfigFactory();
        $phpStanConfig = $phpStanConfigFactory->create();

        $output->writeln('Generating PHPStan temporarily configuration...');
        $configurationWithRaisedLevels = $phpStanConfig->withRaisedLevels();
        $temporaryConfigurationFile = $configurationWithRaisedLevels->toTempFile();

        $output->writeln('Running PHPStan...');
        $process = $this->runAnalysisProcess($temporaryConfigurationFile);

        $output->writeln('Analysing PHPStan output...');

        $phpStanOutput = new PHPStanOutput($phpStanConfig, $process->getOutput());

        $output->writeln('Updating configuration...');
        $phpStanConfig->withAnalysis($phpStanOutput)->save();

        $output->writeln('Cleaning up...');
        unlink($temporaryConfigurationFile);

        $output->writeln('Done!');

        return Command::SUCCESS;
    }

    protected function runAnalysisProcess(string $temporaryConfigurationFile): Process
    {
        $process = (new AnalyseProcessFactory())->create($temporaryConfigurationFile);
        $process->run();

        return $process;
    }
}
