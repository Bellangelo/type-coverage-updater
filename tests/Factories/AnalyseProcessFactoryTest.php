<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageUpdater\Tests\Factories;

use Bellangelo\TypeCoverageUpdater\Factories\AnalyseProcessFactory;
use PHPUnit\Framework\TestCase;

final class AnalyseProcessFactoryTest extends TestCase
{
    public function testCreateAnalyseProcess(): void
    {
        $factory = new AnalyseProcessFactory();
        $process = $factory->create('my-config');

        // phpcs:ignore Generic.Files.LineLength
        $expected = '\'vendor/bin/phpstan\' \'analyse\' \'--error-format\' \'raw\' \'--memory-limit\' \'4G\' \'--level\' \'0\' \'--configuration\' \'my-config\'';

        $this->assertSame($expected, $process->getCommandLine());
    }
}
