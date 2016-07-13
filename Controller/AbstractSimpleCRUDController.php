<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

abstract class AbstractSimpleCRUDController extends AbstractCoreCRUDController implements DashboardAwareControllerInterface, MenuAwareControllerInterface, CrudControllerInterface
{
    protected $menuIcon;

    /**
     * Lists all entities.
     *
     * @Route("/")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        return $this->renderList($request);
    }

    /**
     * Lists all entities.
     *
     * @Route("/csv")
     * @Method("GET")
     */
    public function csvAction(Request $request)
    {
        return $this->handleCsv($request);
    }

    /**
     * Creates a new entity.
     *
     * @Route("/create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request)
    {
        return $this->renderCreate($request);
    }

    /**
     * Finds and displays an entity.
     *
     * @Route("/{id}")
     * @Method("GET")
     */
    public function showAction(Request $request, $id)
    {
        return $this->renderShow($request, ['id' => $id]);
    }

    /**
     * Displays a form to edit an existing entity.
     *
     * @Route("/{id}/edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
        return $this->renderEdit($request, ['id' => $id]);
    }

    /**
     * Deletes an entity.
     *
     * @Route("/{id}")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        return $this->handleDelete($request, ['id' => $id]);
    }

    /**
     * Gets the routes that are displayed on the dashboard.
     */
    public function getDashboardRoutes()
    {
        $routes = [];
        if ($this->hasCrudRoute('list') === true && $this->hasCrudRouteAccess('list') === true) {
            $routes[] = ['title' => 'List', 'route' => $this->getCrudRoute('list')];
        }

        if ($this->hasCrudRoute('create') === true && $this->hasCrudRouteAccess('create') === true) {
            $routes[] = ['title' => 'Create', 'route' => $this->getCrudRoute('create')];
        }

        return $routes;
    }

    /**
     * Gets the routes that are displayed in the menu.
     */
    public function getMenuRoutes()
    {
        $routes = [];
        if ($this->hasCrudRoute('list') === true && $this->hasCrudRouteAccess('list') === true) {
            $routes[] = ['title' => $this->getMenuLabel('list'), 'route' => $this->getCrudRoute('list')];
        }

        return $routes;
    }

    /**
     * Sets the menu icon, used in the service declaration
     *
     * @param string $menuIcon
     *
     * @return AbstractCRUDController
     */
    public function setMenuIcon($menuIcon)
    {
        $this->menuIcon = $menuIcon;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuIcon()
    {
        return $this->menuIcon;
    }

    /**
     * {@inheritdoc}
     */
    public function getDashboardGroupName()
    {
        return $this->getUserFriendlyEntityName();
    }
}
