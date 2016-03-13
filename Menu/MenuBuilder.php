<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Menu;

use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Knp\Menu\FactoryInterface;
use MadrakIO\Bundle\EasyAdminBundle\Chain\ControllerChain;

class MenuBuilder
{
    protected $factory;
    protected $authorizationChecker;
    protected $controllerChain;
    protected $checkGrants;

    /**
     * @param FactoryInterface         $factory
     * @param AuthorizationChecker     $authorizationChecker
     * @param MenuAwareControllerChain $menuAwareControllerChain
     *
     * Add any other dependency you need
     */
    public function __construct(FactoryInterface $factory, AuthorizationChecker $authorizationChecker, ControllerChain $controllerChain, $checkGrants)
    {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->controllerChain = $controllerChain;
        $this->checkGrants = $checkGrants;
    }

    public function createAdminMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

        foreach ($this->controllerChain->getMenuAwareControllers() as $menuAwareController) {
            foreach ($menuAwareController->getMenuRoutes() as $menuRoute) {
                $menu->addChild($menuRoute['title'], ['route' => $menuRoute['route'], 'linkAttributes' => ['icon' => $menuAwareController->getMenuIcon()]]);
            }
        }

        return $menu;
    }
}
