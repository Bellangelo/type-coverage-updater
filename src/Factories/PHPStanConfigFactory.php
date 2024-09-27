<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageUpdater\Factories;

use Bellangelo\TypeCoverageUpdater\Exceptions\ConfigurationFileDoesNotExistException;
use Bellangelo\TypeCoverageUpdater\PHPStan\PHPStanConfig;

final class PHPStanConfigFactory
{
    private const array CONFIG_FILES = [
        'phpstan.neon',
        'phpstan.neon.dist',
    ];

    public function create(): PHPStanConfig
    {
        $config = $this->getConfigFile();

        if (is_null($config)) {
            throw new ConfigurationFileDoesNotExistException();
        }

        return new PHPStanConfig($config);
    }

    private function getConfigFile(): ?string
    {
        foreach (self::CONFIG_FILES as $file) {
            $filepath = stream_resolve_include_path($file);

            if (is_string($filepath)) {
                return $filepath;
            }
        }

        return null;
    }
}
