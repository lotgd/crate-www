<?php
declare(strict_types=1);

namespace LotGD\Crate\WWW\AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;

use LotGD\Core\Bootstrap;
use LotGD\Core\ModuleManager;

/**
 * Description of CoreGameService
 */
class CoreGameService
{
    public $game;
    
    public function __construct()
    {
        $workingDirectory = getcwd();
        if (substr($workingDirectory, -3) == "web") {
            $web = true;
        }
        else {
            $web = false;
        }
        
        // For now
        $handle = fopen(__DIR__ . "/../../../.env", "r");
        while (($line = fgets($handle))) {
            // Quick hack..
            if ($web && substr($line, 0, 13) == "LOTGD_CONFIG=") {
                $line = "LOTGD_CONFIG=../" . substr($line, 13);
            }

            putenv(trim($line));
        }
        
        $this->game = Bootstrap::createGame();
    }
    
    public function getVersion()
    {
        return $this->game->getVersion();
    }
    
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->game->getEntityManager();
    }
    
    public function getModuleManager(): ModuleManager
    {
        return $this->game->getModuleManager();
    }
}
