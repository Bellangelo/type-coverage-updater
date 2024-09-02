<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageAutoUpdate\Tests\Factories;

use Bellangelo\TypeCoverageAutoUpdate\Factories\AnalyseProcessFactory;
use PHPUnit\Framework\TestCase;

class AnalyseProcessFactoryTest extends TestCase
{
    public function testCreateAnalyseProcess(): void
    {
        $factory = new AnalyseProcessFactory();
        $process = $factory->create('my-config');

        $expected = '\'vendor/bin/phpstan\' \'analyse\' \'--error-format\' \'raw\' \'--memory-limit\' \'4G\' \'--level\' \'0\' \'--configuration\' \'my-config\'';

        $this->assertSame($expected, $process->getCommandLine());
    }
}