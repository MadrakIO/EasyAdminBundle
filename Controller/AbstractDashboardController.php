<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

abstract class AbstractDashboardController extends Controller
{
    /**
     * Lists all available CRUD controllers and links to their CREATE and LIST actions.
     *
     * @Route("/")
     * @Method("GET")
     */
    public function indexAction()
    {
        $controllerGroups = [];
        foreach ($this->get('madrak_io_easy_admin.controller_chain')->getDashboardAwareControllers() AS $controller) {
            if (count($controller->getDashboardRoutes()) > 0) {
                $controllerGroups[$controller->getDashboardGroupName()][] = $controller;
            }
        }

        return $this->render('MadrakIOEasyAdminBundle:Dashboard:display.html.twig',
                                [
                                    'parent_template' => $this->getParameter('madrak_io_easy_admin.parent_template'),
                                    'controllerGroups' => $this->sortControllerGroups($controllerGroups),
                                ]);
    }

    /**
     * Returns a sorted controller group array
     *
     * @return array
     */
    protected function sortControllerGroups(array $controllerGroups)
    {
        ksort($controllerGroups);

        return $controllerGroups;
    }
}
