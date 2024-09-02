<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageAutoUpdate\Tests\Factories;

use Bellangelo\TypeCoverageAutoUpdate\Exceptions\ConfigurationFileDoesNotExistException;
use Bellangelo\TypeCoverageAutoUpdate\Factories\PHPStanConfigFactory;
use Bellangelo\TypeCoverageAutoUpdate\PHPStanConfig;
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