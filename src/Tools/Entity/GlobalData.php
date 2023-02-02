<?php

namespace App\Tools\Entity;

use App\Entity\Main\Configs\Module;
use App\Entity\Main\Users\Role;
use App\Entity\Main\Users\UserAction;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class GlobalData
{

    protected $modules = [];
    protected $roles = [];

    protected $actions = [];



    protected static $instance;

    //protected $doctrine;

    public function __construct(ManagerRegistry  $doctrine)
    {
        //$this->doctrine = $doctrine;
        $manager = $doctrine->getManager();
        //$tontineManager = $doctrine->getManager('tontine');
        $this->roles = $this->replaceKey('name', $manager->getRepository(Role::class)->findAll());
        $this->modules = $this->replaceKey('name', $manager->getRepository(Module::class)->findAll());
        $this->actions = $this->replaceKey('name', $manager->getRepository(UserAction::class)->findAll());
        self::$instance = $this;
    }

    public function replaceKey(string $name, array $data)
    {
        $records = [];
        foreach ($data as $item) {
            $method = 'get' . ucfirst($name);
            $records[$item->$method()] = $item;
        }
        return $records;
    }

    public function get($name, string $data)
    {
        return $this->$data[$name] ?? null;
    }

    /**
     * Get the value of modules
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Get the value of actions
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Get the value of instance
     */
    public static function getInstance()
    {
        return self::$instance;
    }
}
