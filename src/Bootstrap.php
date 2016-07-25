<?php

namespace LotGD\Crate\GraphQL;

use Symfony\Component\Console\Application;

use LotGD\Core\BootstrapInterface;
use LotGD\Core\Game;

class Bootstrap implements BootstrapInterface
{
    public function __construct()
    {
        $handle = fopen(__DIR__ . "/../.env", "r");
        while (($line = fgets($handle))) {
            $line = str_replace('$ROOT', __DIR__ . "/..", $line);
            putenv(trim($line));
        }
    }
    
    public function hasEntityPath(): bool
    {
        return true;
    }
    
    public function getEntityPath(): string
    {
        return __DIR__ . "/Models";
    }
    
    public function addDaenerysCommand(Game $game, Application $application) {
    }
}