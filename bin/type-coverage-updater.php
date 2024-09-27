<?php

declare(strict_types=1);

use Bellangelo\TypeCoverageUpdater\Commands\UpdateCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new UpdateCommand());
$application->run();
