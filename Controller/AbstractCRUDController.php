<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Controller;

use \Exception;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\AbstractType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Routing\AnnotatedRouteControllerLoader;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\AbstractShowType;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\AbstractListType;
use MadrakIO\Bundle\EasyAdminBundle\Security\EasyAdminVoterInterface;

abstract class AbstractCRUDController extends Controller implements DashboardAwareControllerInterface, MenuAwareControllerInterface, CrudControllerInterface
{
    const MISSING_CRUD_VIEW = 'The CRUD view for the specified action (%s) does not exist.';

    protected $entityManager;
    protected $entityFormType;
    protected $entityList;
    protected $entityShow;
    protected $entityClass;
    protected $menuIcon;
    protected $crudViews = [
                                'list' => 'MadrakIOEasyAdminBundle:CRUD:list.html.twig',
                                'create' => 'MadrakIOEasyAdminBundle:CRUD:create.html.twig',
                                'show' => 'MadrakIOEasyAdminBundle:CRUD:show.html.twig',
                                'edit' => 'MadrakIOEasyAdminBundle:CRUD:edit.html.twig',
                            ];

    public function __construct(AbstractType $entityFormType, AbstractListType $entityList, AbstractShowType $entityShow, $entityClass)
    {
        $this->entityFormType = $entityFormType;
        $this->entityList = $entityList;
        $this->entityShow = $entityShow;
        $this->entityClass = $entityClass;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->entityManager = $container->get('doctrine.orm.default_entity_manager');
    }

    /**
     * Set a CRUD view.
     *
     * @param string $action
     * @param string $view
     *
     * @return AbstractCRUDController
     */
    public function setCrudView($action, $view)
    {
        $this->crudViews[$action] = $view;

        return $this;
    }

    /**
     * Lists all entities.
     *
     * @Route("/")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        return $this->render($this->getCrudView('list'),
            $this->getCrudViewParameters($request) + ['listView' => $this->entityList->createView($request)]
        );
    }

    /**
     * Creates a new entity.
     *
     * @Route("/create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request)
    {
        $entity = new $this->entityClass();

        $this->denyAccessUnlessGranted(EasyAdminVoterInterface::CREATE, $entity);

        $form = $this->createForm($this->entityFormType, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return $this->redirectToRoute($this->getCrudRoute('show'), array('id' => $entity->getId()));
        }

        return $this->render($this->getCrudView('create'),
            $this->getCrudViewParameters($request) +
                [
                    'entity' => $entity,
                    'form' => $form->createView(),
                ]);
    }

    /**
     * Finds and displays an entity.
     *
     * @Route("/{id}")
     * @Method("GET")
     */
    public function showAction(Request $request, $id)
    {
        $entity = $this->entityManager->getRepository($this->entityClass)->findOneBy(['id' => $id]);

        $this->denyAccessUnlessGranted(EasyAdminVoterInterface::SHOW, $entity);

        $deleteForm = $this->createDeleteForm($request, $entity);

        return $this->render($this->getCrudView('show'),
            $this->getCrudViewParameters($request) +
                [
                    'entity' => $entity,
                    'showView' => $this->entityShow->createView($entity),
                    'delete_form' => $deleteForm->createView(),
                ]);
    }

    /**
     * Displays a form to edit an existing entity.
     *
     * @Route("/{id}/edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->entityManager->getRepository($this->entityClass)->findOneBy(['id' => $id]);

        $this->denyAccessUnlessGranted(EasyAdminVoterInterface::EDIT, $entity);

        $deleteForm = $this->createDeleteForm($request, $entity);
        $editForm = $this->createForm($this->entityFormType, $entity);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return $this->redirectToRoute($this->getCrudRoute('edit'), array('id' => $entity->getId()));
        }

        return $this->render($this->getCrudView('edit'),
            $this->getCrudViewParameters($request) +
                [
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ]);
    }

    /**
     * Deletes an entity.
     *
     * @Route("/{id}")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $entity = $this->entityManager->getRepository($this->entityClass)->findOneBy(['id' => $id]);

        $this->denyAccessUnlessGranted(EasyAdminVoterInterface::DELETE, $entity);

        $form = $this->createDeleteForm($request, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute($this->getCrudRoute('list'));
    }

    /**
     * Get entity class.
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Get user friendly entity name.
     */
    public function getUserFriendlyEntityName()
    {
        $explodedEntityName = explode('\\', $this->entityClass);

        return preg_replace('/([a-z0-9])([A-Z])/', '$1 $2', $explodedEntityName[count($explodedEntityName) - 1]);
    }

    /**
     * Gets related routes based on current route.
     */
    public function getCrudRoutes()
    {
        $annotatedControllerRouteLoader = new AnnotatedRouteControllerLoader(new AnnotationReader());
        $routeCollection = $annotatedControllerRouteLoader->load(get_class($this));

        $routes = [];
        foreach ($routeCollection->all() as $routeName => $routeDetails) {
            $explodedController = explode(':', $routeDetails->getDefaults()['_controller']);
            $routes[str_replace('Action', '', $explodedController[count($explodedController) - 1])] = $routeName;
        }

        return $routes;
    }

    /**
     * Check if the controller has the specified crud route.
     */
    public function hasCrudRoute($routeType)
    {
        return array_key_exists($routeType, $this->getCrudRoutes());
    }

    /**
     * Check if the controller has the specified crud route.
     */
    public function hasCrudRouteAccess($routeType, $object = null)
    {
        if ($this->hasCrudRoute($routeType) === false) {
            return false;
        }

        if (is_null($object) === true) {
            $object = $this->getEntityClass();
        }

        switch ($routeType) {
            case 'create':
                return $this->isGranted(EasyAdminVoterInterface::CREATE, $object);
            case 'show':
            case 'list':
                return $this->isGranted(EasyAdminVoterInterface::SHOW, $object);
            case 'edit':
                return $this->isGranted(EasyAdminVoterInterface::EDIT, $object);
            case 'delete':
                return $this->isGranted(EasyAdminVoterInterface::DELETE, $object);
        }

        return false;
    }

    /**
     * Gets a specific related route based on the current route.
     */
    public function getCrudRoute($routeType)
    {
        return $this->getCrudRoutes()[$routeType];
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
            $routes[] = ['title' => 'List '.$this->getUserFriendlyEntityName(), 'route' => $this->getCrudRoute('list')];
        }

        return $routes;
    }

    /**
     * Returns the CRUD view based on the action.
     *
     * @param string $action
     *
     * @return string $view
     */
    protected function getCrudView($action)
    {
        if (isset($this->crudViews[$action]) === false) {
            throw new Exception(vsprintf(self::MISSING_CRUD_VIEW, [$action]));
        }

        return $this->crudViews[$action];
    }

    /**
     * Creates a form to delete an entity.
     *
     * @param $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm(Request $request, $entity)
    {
        if ($this->isGranted(EasyAdminVoterInterface::DELETE, $entity) === true) {
            return $this->createFormBuilder(null, ['attr' => ['style' => 'display: inline']])
                ->setAction($this->generateUrl($this->getCrudRoute('delete'), array('id' => $entity->getId())))
                ->setMethod('DELETE')
                ->getForm();
        }

        return;
    }

    /**
     * Gets the parameters that are used in every CRUD view.
     *
     * @return array
     */
    protected function getCrudViewParameters(Request $request)
    {
        return [
                    'parent_template' => $this->getParameter('madrak_io_easy_admin.parent_template'),
                    'current_route' => $this->getCurrentRouteName($request),
                    'routes' => $this->getCrudRoutes(),
                    'check_grants' => $this->getParameter('madrak_io_easy_admin.check_grants'),
                    'can_create' => $this->hasCrudRouteAccess('create'),
                ];
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
     * Gets the current route.
     */
    protected function getCurrentRouteName(Request $request)
    {
        return $this->get('router')->matchRequest($request)['_route'];
    }

    /**
     * {@inheritdoc}
     */
    protected function isGranted($attributes, $object = null)
    {
        if ($this->getParameter('madrak_io_easy_admin.check_grants') === true) {
            return parent::isGranted($attributes, $object);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function denyAccessUnlessGranted($attributes, $object = null, $message = 'Access Denied.')
    {
        if ($this->getParameter('madrak_io_easy_admin.check_grants') === true) {
            return parent::denyAccessUnlessGranted($attributes, $object, $message);
        }
    }
}
