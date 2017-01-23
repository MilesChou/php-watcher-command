<?php

require __DIR__ . '/../vendor/autoload.php';

use Watcher\Command;
use Symfony\Component\Console\Application;

$app = new Application();
$app->addCommands([
    new Command\Monolog\Follow(),
]);

$app->run();
