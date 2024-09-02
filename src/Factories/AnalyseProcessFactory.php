<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageUpdater\Factories;

use Symfony\Component\Process\Process;

class AnalyseProcessFactory
{
    private const string MEMORY_LIMIT = '4G';
    private const int TIMEOUT_IN_SECONDS = 600;

    public function create(string $configurationFile): Process
    {
        $command = [
            'vendor/bin/phpstan',
            'analyse',
            '--error-format',
            'raw',
            '--memory-limit',
            self::MEMORY_LIMIT,
            '--level',
            0,
            '--configuration',
            $configurationFile,
        ];

        return new Process($command, null, null, null, self::TIMEOUT_IN_SECONDS);
    }
}
