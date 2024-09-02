<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageAutoUpdate\Factories;

use Symfony\Component\Process\Process;

class AnalyseProcessFactory
{
    private const MEMORY_LIMIT = '4G';
    private const TIMEOUT_IN_SECONDS = 600;

    public function create(string $configurationFile): Process
    {
        $command = [
            'vendor/bin/phpstan',
            'analyse',
            '--error-format',
            'json',
            '--memory-limit',
            self::MEMORY_LIMIT,
            '--level',
            0,
            '--configuration',
            $configurationFile,
        ];

        return new Process($command, '', null, null, self::TIMEOUT_IN_SECONDS);
    }
}