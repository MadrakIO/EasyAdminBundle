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
        $controllers = $this->get('madrak_io_easy_admin.crud_controller_chain')->getCrudControllers();

        return $this->render('MadrakIOEasyAdminBundle:Dashboard:display.html.twig', 
                                [
                                    'parent_template' => $this->getParameter('madrak_io_easy_admin.parent_template'),
                                    'controllers' => $controllers
                                ]);
    }
}