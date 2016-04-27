<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Chain;

use MadrakIO\Bundle\EasyAdminBundle\Controller\CrudControllerInterface;
use MadrakIO\Bundle\EasyAdminBundle\Controller\DashboardAwareControllerInterface;
use MadrakIO\Bundle\EasyAdminBundle\Controller\MenuAwareControllerInterface;

class ControllerChain
{
    private $controllers = [];
    private $crudControllers = [];
    private $dashboardAwareControllers = [];
    private $menuAwareControllers = [];

    public function addController($controller)
    {
        $this->controllers[] = $controller;

        if ($controller instanceof CrudControllerInterface) {
            $this->crudControllers[] = $controller;
        }

        if ($controller instanceof DashboardAwareControllerInterface) {
            $this->dashboardAwareControllers[] = $controller;
        }

        if ($controller instanceof MenuAwareControllerInterface) {
            $this->menuAwareControllers[] = $controller;
        }

        return $this;
    }

    public function getControllers()
    {
        return $this->controllers;
    }

    public function getCrudControllers()
    {
        return $this->crudControllers;
    }

    public function getDashboardAwareControllers()
    {
        return $this->dashboardAwareControllers;
    }

    public function getMenuAwareControllers()
    {
        return $this->menuAwareControllers;
    }
}
