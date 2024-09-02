<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageAutoUpdate\Factories;

use Bellangelo\TypeCoverageAutoUpdate\Exceptions\ConfigurationFileDoesNotExistException;
use Bellangelo\TypeCoverageAutoUpdate\PHPStanConfig;

class PHPStanConfigFactory
{
    private const CONFIG_FILES = [
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