<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageUpdater\Tests\PHPStan;

use Bellangelo\TypeCoverageUpdater\Exceptions\CouldNotOpenConfigurationFileException;
use Bellangelo\TypeCoverageUpdater\PHPStan\PHPStanConfig;
use Bellangelo\TypeCoverageUpdater\PHPStan\PHPStanOutput;
use PHPUnit\Framework\TestCase;

final class PHPStanConfigTest extends TestCase
{
    public function testConfigurationFileDoesNotExist(): void
    {
        $this->expectException(CouldNotOpenConfigurationFileException::class);

        new PHPStanConfig('a-file-that-does-not-exist.neon');
    }

    public function testCanReadTypeCoverageFromFile(): void
    {
        $config = new PHPStanConfig(__DIR__ . '/data/phpstan.neon');

        $this->assertTrue($config->hasParameterTypeCoverage());
        $this->assertTrue($config->hasReturnTypeCoverage());
        $this->assertTrue($config->hasPropertyTypeCoverage());
        $this->assertTrue($config->hasDeclareStrictTypesCoverage());
        $this->assertSame(7.0, $config->getParameterTypeCoverage());
        $this->assertSame(5.0, $config->getPropertyTypeCoverage());
        $this->assertSame(3.0, $config->getReturnTypeCoverage());
        $this->assertSame(1.0, $config->getDeclareStrictTypesCoverage());
    }

    public function testMissingValuesFromFile(): void
    {
        $config = new PHPStanConfig(__DIR__ . '/data/config-with-missing-values.neon');

        $this->assertFalse($config->hasReturnTypeCoverage());
        $this->assertTrue($config->hasParameterTypeCoverage());
        $this->assertTrue($config->hasPropertyTypeCoverage());
        $this->assertTrue($config->hasDeclareStrictTypesCoverage());
        $this->assertSame(null, $config->getReturnTypeCoverage());
        $this->assertSame(0.0, $config->getParameterTypeCoverage());
        $this->assertSame(5.0, $config->getPropertyTypeCoverage());
        $this->assertSame(1.0, $config->getDeclareStrictTypesCoverage());
    }

    public function testWithRaisedLevels(): void
    {
        $config = new PHPStanConfig(__DIR__ . '/data/config-with-missing-values.neon');
        $config = $config->withRaisedLevels();

        $this->assertFalse($config->hasReturnTypeCoverage());
        $this->assertTrue($config->hasParameterTypeCoverage());
        $this->assertTrue($config->hasPropertyTypeCoverage());
        $this->assertTrue($config->hasDeclareStrictTypesCoverage());
        $this->assertSame(null, $config->getReturnTypeCoverage());
        $this->assertSame(100.0, $config->getParameterTypeCoverage());
        $this->assertSame(100.0, $config->getPropertyTypeCoverage());
        $this->assertSame(100.0, $config->getDeclareStrictTypesCoverage());
    }

    public function testToTempFile(): void
    {
        $config = new PHPStanConfig(__DIR__ . '/data/phpstan.neon');
        $filename = $config->toTempFile();

        $this->assertFileExists($filename);
        $this->assertSame(
            file_get_contents(__DIR__ . '/data/phpstan.neon'),
            file_get_contents($filename)
        );

        unlink($filename);
    }

    public function testWithAnalysis(): void
    {
        $output = $this->createMock(PHPStanOutput::class);
        $output->method('hasParameterTypeCoverage')->willReturn(true);
        $output->method('hasReturnTypeCoverage')->willReturn(true);
        $output->method('hasPropertyTypeCoverage')->willReturn(true);
        $output->method('hasDeclareStrictTypesCoverage')->willReturn(true);
        $output->method('getParameterTypeCoverage')->willReturn(60.0);
        $output->method('getReturnTypeCoverage')->willReturn(61.2);
        $output->method('getPropertyTypeCoverage')->willReturn(63.4);
        $output->method('getDeclareStrictTypesCoverage')->willReturn(65.6);

        $config = new PHPStanConfig(__DIR__ . '/data/config-with-missing-values.neon');
        $config = $config->withAnalysis($output);

        $this->assertTrue($config->hasReturnTypeCoverage());
        $this->assertTrue($config->hasParameterTypeCoverage());
        $this->assertTrue($config->hasPropertyTypeCoverage());
        $this->assertTrue($config->hasDeclareStrictTypesCoverage());
        $this->assertSame(61.2, $config->getReturnTypeCoverage());
        $this->assertSame(60.0, $config->getParameterTypeCoverage());
        $this->assertSame(63.4, $config->getPropertyTypeCoverage());
        $this->assertSame(65.6, $config->getDeclareStrictTypesCoverage());
    }

    public function testSave(): void
    {
        copy(__DIR__ . '/data/phpstan.neon', __DIR__ . '/data/temp.neon');

        $config = new PHPStanConfig(__DIR__ . '/data/temp.neon');
        $config->withRaisedLevels()->save();

        $config = new PHPStanConfig(__DIR__ . '/data/temp.neon');
        $this->assertSame(100.0, $config->getReturnTypeCoverage());
        $this->assertSame(100.0, $config->getParameterTypeCoverage());
        $this->assertSame(100.0, $config->getPropertyTypeCoverage());
        $this->assertSame(100.0, $config->getDeclareStrictTypesCoverage());

        unlink(__DIR__ . '/data/temp.neon');
    }
}
