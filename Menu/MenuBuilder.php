<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use MadrakIO\Bundle\EasyAdminBundle\Chain\CrudControllerChain;

class MenuBuilder
{
    protected $factory;
    protected $crudControllerChain;
   
    /**
     * @param FactoryInterface $factory
     *
     * Add any other dependency you need
     */
    public function __construct(FactoryInterface $factory, CrudControllerChain $crudControllerChain)
    {
        $this->factory = $factory;
        $this->crudControllerChain = $crudControllerChain;
    }

    public function createCrudMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

        foreach ($this->crudControllerChain->getCrudControllers() AS $crudController) {
            if ($crudController->hasCrudRoute('list') === true) {
                $menu->addChild('Manage ' . $crudController->getUserFriendlyEntityName(), array('route' => $crudController->getCrudRoute('list')));            
            }
        }

        return $menu;
    }
}
