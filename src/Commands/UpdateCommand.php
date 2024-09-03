<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageUpdater\Commands;

use Bellangelo\TypeCoverageUpdater\Factories\AnalyseProcessFactory;
use Bellangelo\TypeCoverageUpdater\Factories\PHPStanConfigFactory;
use Bellangelo\TypeCoverageUpdater\PHPStan\PHPStanOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

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
        $configurationFactory = new PHPStanConfigFactory();
        $originalConfiguration = $configurationFactory->create();

        $output->writeln('Generating PHPStan temporarily configuration...');
        $configurationWithRaisedLevels = $originalConfiguration->withRaisedLevels();
        $temporaryConfigurationFile = $configurationWithRaisedLevels->toTempFile();

        $output->writeln('Running PHPStan...');
        $process = $this->runAnalysisProcess($temporaryConfigurationFile);

        $output->writeln('Analysing PHPStan output...');

        $analysisOutput = new PHPStanOutput($originalConfiguration, $process->getOutput());

        $output->writeln('Updating configuration...');
        $originalConfiguration->withAnalysis($analysisOutput)->save();

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
