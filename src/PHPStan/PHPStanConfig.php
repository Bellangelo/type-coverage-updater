<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageUpdater\PHPStan;

use Bellangelo\TypeCoverageUpdater\Exceptions\CouldNotOpenConfigurationFileException;
use Exception;
use Nette\Neon\Neon;

class PHPStanConfig
{
    private const string TEMP_FILE = 'phpstan.temp.neon';

    /**
     * @var array{
     *     parameters?: array<string, array>
     * }
     */
    private array $configuration;

    public function __construct(private string $configurationFile)
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

    public function withRaisedLevels(): self
    {
        $self = clone $this;

        if ($self->hasReturnTypeCoverage()) {
            $self->configuration['parameters']['type_coverage']['return_type'] = 100;
        }

        if ($self->hasParameterTypeCoverage()) {
            $self->configuration['parameters']['type_coverage']['param_type'] = 100;
        }

        if ($self->hasPropertyTypeCoverage()) {
            $self->configuration['parameters']['type_coverage']['property_type'] = 100;
        }

        if ($self->hasDeclareStrictTypesCoverage()) {
            $self->configuration['parameters']['type_coverage']['declare'] = 100;
        }

        return $self;
    }

    public function withAnalysis(PHPStanOutput $output): self
    {
        $self = clone $this;

        if ($output->hasReturnTypeCoverage()) {
            $self->configuration['parameters']['type_coverage']['return_type'] = $output->getReturnTypeCoverage();
        } elseif ($self->hasReturnTypeCoverage()) {
            $self->configuration['parameters']['type_coverage']['return_type'] = 100.0;
        }

        if ($output->hasParameterTypeCoverage()) {
            $self->configuration['parameters']['type_coverage']['param_type'] = $output->getParameterTypeCoverage();
        } elseif ($self->hasParameterTypeCoverage()) {
            $self->configuration['parameters']['type_coverage']['param_type'] = 100.0;
        }

        if ($output->hasPropertyTypeCoverage()) {
            $self->configuration['parameters']['type_coverage']['property_type'] = $output->getPropertyTypeCoverage();
        } elseif ($self->hasPropertyTypeCoverage()) {
            $self->configuration['parameters']['type_coverage']['property_type'] = 100.0;
        }

        if ($output->hasDeclareStrictTypesCoverage()) {
            $self->configuration['parameters']['type_coverage']['declare'] = $output->getDeclareStrictTypesCoverage();
        } elseif ($self->hasDeclareStrictTypesCoverage()) {
            $self->configuration['parameters']['type_coverage']['declare'] = 100.0;
        }

        return $self;
    }

    public function toTempFile(): string
    {
        $this->save(self::TEMP_FILE);

        return self::TEMP_FILE;
    }

    public function save(?string $filename = null): void
    {
        file_put_contents(
            $filename ?? $this->configurationFile,
            Neon::encode($this->configuration, true)
        );
    }
}
