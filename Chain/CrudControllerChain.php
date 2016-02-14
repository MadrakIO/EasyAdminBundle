<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Chain;

use MadrakIO\Bundle\EasyAdminBundle\Controller\AbstractCRUDController;

class CrudControllerChain
{
    private $controllers;

    public function __construct()
    {
        $this->controllers = [];
    }

    public function addCrudController(AbstractCRUDController $controller)
    {
        $this->controllers[] = $controller;
        
        return $this;
    }
    
    public function getCrudControllers()
    {
        return $this->controllers;
    }
}