<?php

declare(strict_types=1);

use Bellangelo\TypeCoverageAutoUpdate\Commands\UpdateCommand;
use Symfony\Component\Console\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$application = new Application();
$application->add(new UpdateCommand());
$application->run();