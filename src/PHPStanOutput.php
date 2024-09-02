<?php


namespace Bellangelo\TypeCoverageAutoUpdate;

class PHPStanOutput
{
    private  $parameterTypesCoverage = null;
    private ?float $returnTypesCoverage = null;
    private ?float $propertyTypesCoverage = null;
    private ?float $declareStrictTypesCoverage = null;

    public function __construct($config, string $output)
    {
        $this->parseOutput($config, $output);
    }

    private function parseOutput(PHPStanConfig $config, string $output)
    {
        /** @var array<string> $lines */
        $lines = preg_split("/((\r?\n)|(\r\n?))/", $output);

        foreach($lines as $line){

            if ($config->hasParameterTypeCoverage() && !$this->hasParameterTypeCoverage()) {
                $this->parameterTypesCoverage = $this->extractParameterTypeCoverage($line);

                if ($this->hasParameterTypeCoverage()) {
                    continue;
                }
            }

            if ($config->hasReturnTypeCoverage() && !$this->hasReturnTypeCoverage()) {
                $this->returnTypesCoverage = $this->extractReturnTypeCoverage($line);

                if ($this->hasReturnTypeCoverage()) {
                    continue;
                }
            }

            if ($config->hasPropertyTypeCoverage() && !$this->hasPropertyTypeCoverage()) {
                $this->propertyTypesCoverage = $this->extractPropertyTypeCoverage($line);

                if ($this->hasPropertyTypeCoverage()) {
                    continue;
                }
            }

            if ($config->hasDeclareStrictTypesCoverage() && !$this->hasDeclareStrictTypesCoverage()) {
                $this->declareStrictTypesCoverage = $this->declareStrictTypesCoverage($line);
            }

            // Break early as errors are repeated.
            if (
                $this->hasParameterTypeCoverage()
                && $this->hasReturnTypeCoverage()
                && $this->hasPropertyTypeCoverage()
                && $this->hasDeclareStrictTypesCoverage()
            ) {
                break;
            }
        }
    }

    private function extractParameterTypeCoverage(string $line): ?float
    {
        $matches = [];

        if (preg_match('/.*Out of (\d+) possible param types, only (\d+) - (\d+\.?\d*)\s*%\s*actually have it\./', $line, $matches)) {
            return (float) $matches[3];
        }

        return null;
    }

    private function extractReturnTypeCoverage(string $line): ?float
    {
        $matches = [];

        if (preg_match('/.*Out of (\d+) possible return types, only (\d+) - (\d+\.?\d*)\s*%\s*actually have it\./', $line, $matches)) {
            return (float) $matches[3];
        }

        return null;
    }

    private function extractPropertyTypeCoverage(string $line): ?float
    {
        $matches = [];

        if (preg_match('/.*Out of (\d+) possible property types, only (\d+) - (\d+\.?\d*)\s*%\s*actually have it\./', $line, $matches)) {
            return (float) $matches[3];
        }

        return null;
    }

    private function declareStrictTypesCoverage(string $line): ?float
    {
        $matches = [];

        if (preg_match('/.*Out of (\d+) possible declare\(strict_types=1\), only (\d+) - (\d+\.?\d*)\s*%\s*actually have it\./', $line, $matches)) {
            return (float) $matches[3];
        }

        return null;
    }

    public function hasParameterTypeCoverage(): bool
    {
        return !is_null($this->parameterTypesCoverage);
    }

    public function hasReturnTypeCoverage(): bool
    {
        return !is_null($this->returnTypesCoverage);
    }

    public function hasPropertyTypeCoverage(): bool
    {
        return !is_null($this->propertyTypesCoverage);
    }

    public function hasDeclareStrictTypesCoverage(): bool
    {
        return !is_null($this->declareStrictTypesCoverage);
    }

    public function getParameterTypeCoverage(): ?float
    {
        return $this->parameterTypesCoverage;
    }

    public function getReturnTypeCoverage(): ?float
    {
        return $this->returnTypesCoverage;
    }

    public function getPropertyTypeCoverage(): ?float
    {
        return $this->propertyTypesCoverage;
    }

    public function getDeclareStrictTypesCoverage(): ?float
    {
        return $this->declareStrictTypesCoverage;
    }
}