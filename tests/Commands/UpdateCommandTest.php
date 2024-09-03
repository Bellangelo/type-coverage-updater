<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageUpdater\Tests\Commands;

use Bellangelo\TypeCoverageUpdater\Commands\UpdateCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;

class UpdateCommandTest extends TestCase
{
    private function getCommand(): UpdateCommand
    {
        $command = $this
            ->getMockBuilder(UpdateCommand::class)
            ->enableOriginalConstructor()
            ->onlyMethods(['runAnalysisProcess'])
            ->getMock();

        return $command;
    }

    private function getProcess(string $configurationFile): Process
    {
        $command = [
            __DIR__ . '/../../vendor/bin/phpstan',
            'analyse',
            '--error-format',
            'raw',
            '--memory-limit',
            '500M',
            '--level',
            0,
            '--configuration',
            $configurationFile,
        ];

        $process = new Process($command, null, null, null, 20);
        $process->run();

        return $process;
    }

    public static function getProjects(): array
    {
        return [
            ['project1'],
            ['project2'],
        ];
    }

    /**
     * @dataProvider getProjects
     */
    public function testProject(string $folderProject): void
    {
        $originalWorkingDirectory = getcwd();
        $workingDirectory = __DIR__ . '/data/' . $folderProject;
        chdir($workingDirectory);

        copy(__DIR__ . '/data/phpstan.neon', $workingDirectory . '/phpstan.neon');

        $command = $this->getCommand();
        $command->method('runAnalysisProcess')->willReturnCallback(function (string $temporaryConfigurationFile): Process {
            return $this->getProcess($temporaryConfigurationFile);
        });
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        try {
            $commandTester->assertCommandIsSuccessful();

            $this->assertSame(
                file_get_contents($workingDirectory . '/expected.neon'),
                file_get_contents($workingDirectory . '/phpstan.neon')
            );
        } finally {
            unlink($workingDirectory . '/phpstan.neon');
            chdir($originalWorkingDirectory);
        }
    }
}
