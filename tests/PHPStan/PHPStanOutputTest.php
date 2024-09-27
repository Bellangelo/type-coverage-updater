<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageUpdater\Tests\PHPStan;

use Bellangelo\TypeCoverageUpdater\PHPStan\PHPStanConfig;
use Bellangelo\TypeCoverageUpdater\PHPStan\PHPStanOutput;
use PHPUnit\Framework\TestCase;

final class PHPStanOutputTest extends TestCase
{
    public function testHappyPath(): void
    {
        $output = (string) file_get_contents(__DIR__ . '/data/phpstan-output.txt');
        $config = $this->createMock(PHPStanConfig::class);
        $config->method('hasParameterTypeCoverage')->willReturn(true);
        $config->method('hasReturnTypeCoverage')->willReturn(true);
        $config->method('hasPropertyTypeCoverage')->willReturn(true);
        $config->method('hasDeclareStrictTypesCoverage')->willReturn(true);

        $phpStanOutput = new PHPStanOutput($config, $output);

        $this->assertTrue($phpStanOutput->hasParameterTypeCoverage());
        $this->assertTrue($phpStanOutput->hasReturnTypeCoverage());
        $this->assertTrue($phpStanOutput->hasPropertyTypeCoverage());
        $this->assertTrue($phpStanOutput->hasDeclareStrictTypesCoverage());
        $this->assertSame(80.0, $phpStanOutput->getPropertyTypeCoverage());
        $this->assertSame(96.4, $phpStanOutput->getReturnTypeCoverage());
        $this->assertSame(92.8, $phpStanOutput->getParameterTypeCoverage());
        $this->assertSame(85.7, $phpStanOutput->getDeclareStrictTypesCoverage());
    }

    public function testConfigDoesNotHaveAllOptions(): void
    {
        $output = (string) file_get_contents(__DIR__ . '/data/phpstan-output.txt');
        $config = $this->createMock(PHPStanConfig::class);
        $config->method('hasParameterTypeCoverage')->willReturn(true);
        $config->method('hasReturnTypeCoverage')->willReturn(true);
        $config->method('hasPropertyTypeCoverage')->willReturn(false);
        $config->method('hasDeclareStrictTypesCoverage')->willReturn(false);

        $phpStanOutput = new PHPStanOutput($config, $output);

        $this->assertTrue($phpStanOutput->hasParameterTypeCoverage());
        $this->assertTrue($phpStanOutput->hasReturnTypeCoverage());
        $this->assertFalse($phpStanOutput->hasPropertyTypeCoverage());
        $this->assertFalse($phpStanOutput->hasDeclareStrictTypesCoverage());
        $this->assertSame(null, $phpStanOutput->getPropertyTypeCoverage());
        $this->assertSame(96.4, $phpStanOutput->getReturnTypeCoverage());
        $this->assertSame(92.8, $phpStanOutput->getParameterTypeCoverage());
        $this->assertSame(null, $phpStanOutput->getDeclareStrictTypesCoverage());
    }
}
