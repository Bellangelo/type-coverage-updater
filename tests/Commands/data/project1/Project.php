<?php

namespace Bellangelo\TypeCoverageUpdater\Tests\Commands\data\project1;

class Project
{
    private string $other;
    private $otherOfOther;

    public function getSomething(): string
    {
        return 'Project 1';
    }

    public function getSomethingElse($something, string $else, int $other)
    {
        return 'Project 1';
    }

    public function getSomethingElseElse($something, $else, $other): string
    {
        return 'Project 1';
    }
}
