#!/usr/bin/env php
<?php

use LotGD\Core\Bootstrap;
use LotGD\Core\Console\Main as DanerysConsole;

function includeIfExists($file) 
{
    if (file_exists($file)) {
        return include $file;
    }
}

// Dance to find the autoloader.
// TOOD: change this to open up the Composer config and use $c['config']['vendor-dir'] instead of "vendor"
includeIfExists(getcwd() . '/vendor/autoload.php') ||
includeIfExists(__DIR__ . '/../vendor/autoload.php') ||
includeIfExists(__DIR__ . '/../autoload.php');

// For now
$handle = fopen(__DIR__ . "/../.env", "r");
while (($line = fgets($handle))) {
    putenv(trim($line));
}

$loader = function () {
    return Bootstrap::createGame();
};

DanerysConsole::main($loader);