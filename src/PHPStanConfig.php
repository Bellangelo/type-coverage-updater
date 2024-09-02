<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageAutoUpdate;

use Bellangelo\TypeCoverageAutoUpdate\Exceptions\CouldNotOpenConfigurationFileException;
use Exception;
use Nette\Neon\Neon;

class PHPStanConfig
{
    /**
     * @var array<mixed>
     */
    private array $configuration;

    public function __construct(string $configurationFile)
    {
        $this->loadConfiguration($configurationFile);
    }

    private function loadConfiguration(string $configurationFile): void
    {
        try {
            $configuration = (array) Neon::decodeFile($configurationFile);
        } catch (Exception $e) {
            throw new CouldNotOpenConfigurationFileException($e->getMessage());
        }

        $this->configuration = $configuration;
    }

    private function getTypeCoverageParameter(string $typeCoverage): ?float
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

        return (float) $this->configuration['parameters']['type_coverage'][$typeCoverage];
    }

    public function getReturnTypeCoverage(): ?float
    {
        return $this->getTypeCoverageParameter('return_type');
    }

    public function getParameterTypeCoverage(): ?float
    {
        return $this->getTypeCoverageParameter('param_type');
    }

    public function getPropertyTypeCoverage(): ?float
    {
        return $this->getTypeCoverageParameter('property_type');
    }

    public function getDeclareStrictTypesCoverage(): ?float
    {
        return $this->getTypeCoverageParameter('declare');
    }

    public function hasReturnTypeCoverage(): bool
    {
        return !is_null($this->getReturnTypeCoverage());
    }

    public function hasParameterTypeCoverage(): bool
    {
        return !is_null($this->getParameterTypeCoverage());
    }

    public function hasPropertyTypeCoverage(): bool
    {
        return !is_null($this->getPropertyTypeCoverage());
    }

    public function hasDeclareStrictTypesCoverage(): bool
    {
        return !is_null($this->getDeclareStrictTypesCoverage());
    }
}