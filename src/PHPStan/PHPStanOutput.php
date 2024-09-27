<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageUpdater\PHPStan;

class PHPStanOutput
{
    private ?float $parameterTypesCoverage = null;

    private ?float $returnTypesCoverage = null;

    private ?float $propertyTypesCoverage = null;

    private ?float $declareStrictTypesCoverage = null;

    public function __construct(PHPStanConfig $phpStanConfig, string $output)
    {
        $this->parseOutput($phpStanConfig, $output);
    }

    private function parseOutput(PHPStanConfig $phpStanConfig, string $output): void
    {
        /** @var array<string> $lines */
        $lines = preg_split("/((\r?\n)|(\r\n?))/", $output);

        foreach ($lines as $line) {
            if ($phpStanConfig->hasParameterTypeCoverage() && !$this->hasParameterTypeCoverage()) {
                $this->parameterTypesCoverage = $this->extractParameterTypeCoverage($line);

                if ($this->hasParameterTypeCoverage()) {
                    continue;
                }
            }

            if ($phpStanConfig->hasReturnTypeCoverage() && !$this->hasReturnTypeCoverage()) {
                $this->returnTypesCoverage = $this->extractReturnTypeCoverage($line);

                if ($this->hasReturnTypeCoverage()) {
                    continue;
                }
            }

            if ($phpStanConfig->hasPropertyTypeCoverage() && !$this->hasPropertyTypeCoverage()) {
                $this->propertyTypesCoverage = $this->extractPropertyTypeCoverage($line);

                if ($this->hasPropertyTypeCoverage()) {
                    continue;
                }
            }

            if ($phpStanConfig->hasDeclareStrictTypesCoverage() && !$this->hasDeclareStrictTypesCoverage()) {
                $this->declareStrictTypesCoverage = $this->extractDeclareStrictTypesCoverage($line);
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
        $pattern = '/.*Out of (\d+) possible param types, only (\d+) - (\d+\.?\d*)\s*%\s*actually have it\./';

        return $this->extractCoverage($pattern, $line);
    }

    private function extractReturnTypeCoverage(string $line): ?float
    {
        $pattern = '/.*Out of (\d+) possible return types, only (\d+) - (\d+\.?\d*)\s*%\s*actually have it\./';

        return $this->extractCoverage($pattern, $line);
    }

    private function extractPropertyTypeCoverage(string $line): ?float
    {
        $pattern = '/.*Out of (\d+) possible property types, only (\d+) - (\d+\.?\d*)\s*%\s*actually have it\./';

        return $this->extractCoverage($pattern, $line);
    }

    private function extractDeclareStrictTypesCoverage(string $line): ?float
    {
        // phpcs:ignore Generic.Files.LineLength
        $pattern = '/.*Out of (\d+) possible declare\(strict_types=1\), only (\d+) - (\d+\.?\d*)\s*%\s*actually have it\./';

        return $this->extractCoverage($pattern, $line);
    }

    private function extractCoverage(string $pattern, string $line): ?float
    {
        $matches = [];

        if (preg_match($pattern, $line, $matches)) {
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
