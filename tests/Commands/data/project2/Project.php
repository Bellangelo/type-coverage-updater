<?php

declare(strict_types=1);

namespace Bellangelo\TypeCoverageUpdater\Tests\Commands\data\project2;

final class Project
{
    private string $other;

    public function getSomething(string $ok): string
    {
        return $ok;
    }
}
