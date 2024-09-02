<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageUpdater\Tests\Factories;

use Bellangelo\TypeCoverageUpdater\Exceptions\ConfigurationFileDoesNotExistException;
use Bellangelo\TypeCoverageUpdater\Factories\PHPStanConfigFactory;
use Bellangelo\TypeCoverageUpdater\PHPStanConfig;
use PHPUnit\Framework\TestCase;

class PHPStanConfigFactoryTest extends TestCase
{
    public function testCouldNotFindConfigFile(): void
    {
        $originalWorkingDirectory = getcwd();
        chdir(__DIR__);

        $this->expectException(ConfigurationFileDoesNotExistException::class);

        $factory = new PHPStanConfigFactory();
        $factory->create();

        chdir($originalWorkingDirectory);
    }

    public function testHappyPath(): void
    {
        $originalWorkingDirectory = getcwd();
        chdir(__DIR__ . '/../data/');

        $factory = new PHPStanConfigFactory();
        $config = $factory->create();

        $this->assertInstanceOf(PHPStanConfig::class, $config);

        chdir($originalWorkingDirectory);
    }
}