<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\Services;

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
        if (substr(getcwd(), -4) === "/web") { 
            $this->game = Bootstrap::createGame(getcwd() . "/..");
        }
        else {
            $this->game = Bootstrap::createGame(getcwd());
        }
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
