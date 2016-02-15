<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Menu;

use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Knp\Menu\FactoryInterface;
use MadrakIO\Bundle\EasyAdminBundle\Chain\CrudControllerChain;
use MadrakIO\Bundle\EasyAdminBundle\Controller\AbstractCRUDController;
use MadrakIO\Bundle\EasyAdminBundle\Security\EasyAdminVoterInterface;

class MenuBuilder
{
    protected $factory;
    protected $authorizationChecker;
    protected $crudControllerChain;
    protected $checkGrants;
    
    /**
     * @param FactoryInterface $factory
     * @param AuthorizationChecker $authorizationChecker
     * @param CrudControllerChain $crudControllerChain
     *
     * Add any other dependency you need
     */
    public function __construct(FactoryInterface $factory, AuthorizationChecker $authorizationChecker, CrudControllerChain $crudControllerChain, $checkGrants)
    {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->crudControllerChain = $crudControllerChain;
        $this->checkGrants = $checkGrants;
    }

    public function createCrudMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

        foreach ($this->crudControllerChain->getCrudControllers() AS $crudController) {
            if ($crudController->hasCrudRoute('list') === true && $this->isGranted($crudController) === true) {
                $menu->addChild('Manage ' . $crudController->getUserFriendlyEntityName(), array('route' => $crudController->getCrudRoute('list')));            
            }
        }

        return $menu;
    }
    
    public function isGranted(AbstractCRUDController $crudController)
    {
        if ($this->checkGrants === true) {
            return $this->authorizationChecker->isGranted(EasyAdminVoterInterface::MENU, $crudController->getEntityClass());
        }
        
        return true;
    }
}
