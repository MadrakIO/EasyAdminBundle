<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Controller;

use \Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\AbstractShowType;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\AbstractListType;
use MadrakIO\Bundle\EasyAdminBundle\Security\EasyAdminVoterInterface;

abstract class AbstractCoreCRUDController extends AbstractController implements CrudControllerInterface
{
    const MISSING_CRUD_VIEW = 'The CRUD view for the specified action (%s) does not exist.';

    protected $entityFormType;
    protected $entityList;
    protected $entityShow;
    protected $entityClass;
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
     * @param Request $request
     *
     * @return Response
     */
    public function renderList(Request $request, array $criteria = [])
    {
        $entity = new $this->entityClass();
        $filterForm = $this->createFilterForm($request, $entity);

        return $this->render($this->getCrudView('list'),
            $this->getCrudViewParameters($request) +
            $this->getCrudViewRouteParameters($request) +
                [
                    'listView' => $this->entityList->createView($request, $criteria),
                    'filter_form' => $filterForm->createView()
                ]);
    }

    /**
     * Creates a new entity.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function renderCreate(Request $request)
    {
        $entity = new $this->entityClass();

        $this->denyAccessUnlessGranted(EasyAdminVoterInterface::CREATE, $entity);

        $form = $this->createForm($this->entityFormType, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            $routeInfo = $this->getCreateRouteRedirect($request, $entity);

            return $this->redirectToRoute($routeInfo['route'], $routeInfo['parameters']);
        }

        return $this->render($this->getCrudView('create'),
            $this->getCrudViewParameters($request) +
            $this->getCrudViewRouteParameters($request) +
                [
                    'entity' => $entity,
                    'form' => $form->createView(),
                ]);
    }

    /**
     * Finds and displays an entity.
     *
     * @param Request $request
     * @param array $criteria
     *
     * @return Response
     */
    public function renderShow(Request $request, array $criteria)
    {
        $entity = $this->entityManager->getRepository($this->entityClass)->findOneBy($criteria);

        $this->denyAccessUnlessGranted(EasyAdminVoterInterface::SHOW, $entity);

        $deleteForm = $this->createDeleteForm($request, $entity);

        return $this->render($this->getCrudView('show'),
            $this->getCrudViewParameters($request) +
            $this->getCrudViewRouteParameters($request) +
                [
                    'entity' => $entity,
                    'showView' => $this->entityShow->createView($entity),
                    'delete_form' => $deleteForm->createView(),
                ]);
    }

    /**
     * Displays a form to edit an existing entity.
     *
     * @param Request $request
     * @param array $criteria
     *
     * @return Response
     */
    public function renderEdit(Request $request, array $criteria)
    {
        $entity = $this->entityManager->getRepository($this->entityClass)->findOneBy($criteria);

        $this->denyAccessUnlessGranted(EasyAdminVoterInterface::EDIT, $entity);

        $deleteForm = $this->createDeleteForm($request, $entity);
        $editForm = $this->createForm($this->entityFormType, $entity);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return $this->redirectToRoute($this->getCrudRoute('edit'), $this->getCurrentRouteParameters($request));
        }

        return $this->render($this->getCrudView('edit'),
            $this->getCrudViewParameters($request) +
            $this->getCrudViewRouteParameters($request) +
                [
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ]);
    }

    /**
     * Deletes an entity.
     *
     * @param Request $request
     * @param array $criteria
     *
     * @return Response
     */
    public function handleDelete(Request $request, array $criteria)
    {
        $entity = $this->entityManager->getRepository($this->entityClass)->findOneBy($criteria);

        $this->denyAccessUnlessGranted(EasyAdminVoterInterface::DELETE, $entity);

        $form = $this->createDeleteForm($request, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute($this->getCrudRoute('list'), $this->getCurrentRouteParameters($request));
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
     * Check if the controller has the specified crud route.
     */
    public function hasCrudRoute($routeType)
    {
        return array_key_exists($routeType, $this->getRelatedRoutes());
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
        return $this->getRelatedRoutes()[$routeType];
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
     * @param Request $request
     * @param $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm(Request $request, $entity)
    {
        if ($this->isGranted(EasyAdminVoterInterface::DELETE, $entity) === true) {
            return $this->createFormBuilder(null, ['attr' => ['style' => 'display: inline']])
                ->setAction($this->generateUrl($this->getCrudRoute('delete'), $this->getCurrentRouteParameters($request)))
                ->setMethod('DELETE')
                ->getForm();
        }

        return;
    }

    /**
     * Creates a form to delete an entity.
     *
     * @param Request $request
     * @param $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createFilterForm(Request $request, $entity)
    {
        if ($this->isGranted(EasyAdminVoterInterface::SHOW, $entity) === true) {
            return $this->createFormBuilder(null, ['attr' => ['style' => 'display: inline']])
                ->setAction($this->generateUrl($this->getCrudRoute('list'), $this->getCurrentRouteParameters($request)))
                ->setMethod('LIST')
                ->getForm();
        }

        return;
    }

    /**
     * Get generated routes for top nav
     *
     * @param object entity
     *
     * @return array
     */
    protected function getCrudViewRouteParameters(Request $request)
    {
        $generatedRoutes = [];

        foreach ($this->getRelatedRoutes() AS $routeKey => $route) {
            if (in_array($routeKey, ['create', 'edit', 'show', 'list']) === false) {
                continue;
            }

            try {
                $generatedRoutes[$routeKey] = $this->generateUrl($route, $this->getCurrentRouteParameters($request));
            } catch(MissingMandatoryParametersException $exception) {
                //Do nothing
            }
        }

        return ['generated_routes' => $generatedRoutes];
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
                    'routes' => $this->getRelatedRoutes(),
                    'check_grants' => $this->getParameter('madrak_io_easy_admin.check_grants'),
                    'can_create' => $this->hasCrudRouteAccess('create'),
                ];
    }
}
