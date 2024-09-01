<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageAutoUpdate;

use Bellangelo\TypeCoverageAutoUpdate\Exceptions\CouldNotOpenConfigurationFile;
use Exception;
use Nette\Neon\Neon;

class PHPStanConfig
{
    private array $configuration;

    public function __construct(string $configurationFile)
    {
        $this->loadConfiguration($configurationFile);
    }

    private function loadConfiguration(string $configurationFile): void
    {
        try {
            $configuration = Neon::decodeFile($configurationFile);
        } catch (Exception $e) {
            throw new CouldNotOpenConfigurationFile($e->getMessage());
        }

        $this->configuration = $configuration;
    }

    private function getTypeCoverageParameter(string $typeCoverage): ?int
    {
        if (!array_key_exists('parameters', $this->configuration) || !is_array($this->configuration['parameters'])) {
            return null;
        }

        if (
            !array_key_exists('type_coverage', $this->configuration['parameters'])
            || !is_array($this->configuration['parameters']['type_coverage'])
        ) {
            return null;
        }

        if (!array_key_exists($typeCoverage, $this->configuration['parameters']['type_coverage'])) {
            return null;
        }

        return (int) $this->configuration['parameters']['type_coverage'][$typeCoverage];
    }

    public function hasReturnTypeCoverage(): bool
    {
        return $this->getTypeCoverageParameter('return_type') !== null;
    }

    public function hasParameterTypeCoverage(): bool
    {
        return $this->getTypeCoverageParameter('param_type') !== null;
    }

    public function hasPropertyTypeCoverage(): bool
    {
        return $this->getTypeCoverageParameter('property_type') !== null;
    }

    public function hasDeclareStrictTypesCoverage(): bool
    {
        return $this->getTypeCoverageParameter('declare') !== null;
    }
}