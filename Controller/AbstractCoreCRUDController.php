<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Controller;

use \Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\AbstractShowType;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\AbstractListType;
use MadrakIO\Bundle\EasyAdminBundle\Security\EasyAdminVoterInterface;

abstract class AbstractCoreCRUDController extends AbstractController implements CrudControllerInterface
{
    const CREATE_RECORD_SUCCESS_MSG = 'Your record was successfully created.';
    const CREATE_RECORD_ERROR_MSG = 'There was an error attempting to create your record: %s.';
    const UPDATE_RECORD_SUCCESS_MSG = 'Your record was successfully updated.';
    const UPDATE_RECORD_ERROR_MSG = 'There was an error attempting to update your record: %s.';
    const MISSING_CRUD_VIEW = 'The CRUD view for the specified action (%s) does not exist.';
    const UNEXPECTED_GRANT_ATTRIBUTE = 'The attribute specified (%s) was not expected.';

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
        $this->prependBreadcrumb();

        return $this->render($this->getCrudView('list'),
            $this->getCrudViewParameters($request) +
            $this->getCrudViewRouteParameters($request) +
                [
                    'title' => $this->getPageTitle('list'),
                    'listView' => $this->entityList->createView($request, $criteria),
                    'list_is_filterable' => $this->entityList->isFilterable(),
                    'list_is_exportable' => $this->entityList->isExportable(),
                    'filter_is_active' => $this->entityList->isSubmitted($request),
                    'filter_params' => $request->server->get('QUERY_STRING')
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
        $this->prependAndAddBreadcrumbItem("New " . $this->getUserFriendlyEntityName());

        $entity = new $this->entityClass();

        $this->denyAccessUnlessGranted($this->getGrantAttribute(EasyAdminVoterInterface::CREATE), $entity);

        $form = $this->createForm(get_class($this->entityFormType), $entity, $this->getCreateFormOptions());
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
                $routeInfo = $this->getCreateRouteRedirect($request, $entity);

                $this->alertSuccess(static::CREATE_RECORD_SUCCESS_MSG);

                return $this->redirectToRoute($routeInfo['route'], $routeInfo['parameters']);
            }

            $this->alertError(static::CREATE_RECORD_ERROR_MSG, [$form->getErrors()->__toString()]);
        }

        return $this->render($this->getCrudView('create'),
            $this->getCrudViewParameters($request) +
            $this->getCrudViewRouteParameters($request) +
                [
                    'title' => $this->getPageTitle('create'),
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
        $this->prependAndAddBreadcrumbItem($this->getUserFriendlyEntityName());

        $entity = $this->entityManager->getRepository($this->entityClass)->findOneBy($criteria);

        $this->denyAccessUnlessGranted($this->getGrantAttribute(EasyAdminVoterInterface::SHOW), $entity);

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
        $this->prependAndAddBreadcrumbItem("Edit " . $this->getUserFriendlyEntityName());

        $entity = $this->entityManager->getRepository($this->entityClass)->findOneBy($criteria);

        $this->denyAccessUnlessGranted($this->getGrantAttribute(EasyAdminVoterInterface::EDIT), $entity);

        $deleteForm = $this->createDeleteForm($request, $entity);
        $editForm = $this->createForm(get_class($this->entityFormType), $entity, $this->getEditFormOptions());
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            if ($editForm->isValid()) {
                $this->entityManager->persist($entity);
                $this->entityManager->flush();

                $this->alertSuccess(static::UPDATE_RECORD_SUCCESS_MSG);

                return $this->redirectToRoute($this->getCrudRoute('edit'), $this->getCurrentRouteParameters($request));
            }

            $this->alertError(static::UPDATE_RECORD_ERROR_MSG, [$editForm->getErrors()->__toString()]);
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
    public function handleDelete(Request $request, array $criteria, $redirect = true)
    {
        $entity = $this->entityManager->getRepository($this->entityClass)->findOneBy($criteria);

        $this->denyAccessUnlessGranted($this->getGrantAttribute(EasyAdminVoterInterface::DELETE), $entity);

        $form = $this->createDeleteForm($request, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        }

        if ($redirect === false) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute($this->getCrudRoute('list'), $this->getCurrentRouteParameters($request));
    }

    /**
     * Export csv with entities.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handleCsv(Request $request, array $criteria = [])
    {
        $this->entityList->build();
        $response = new StreamedResponse();
        $response->setCallback(function() use ($request, $criteria) {
            $handle = fopen('php://output', 'w+');

            $fields = $this->entityList->getCsvFields();

            fputcsv($handle, $fields);

            $results = $this->entityList->createQueryBuilder($request, $criteria)->getQuery()->getResult();

            foreach($results as $entity) {
                $row = array();

                foreach($fields as $field) {
                    $getField = 'get' . ucfirst($field);
                    if (method_exists($entity, $getField)) {
                        $value = $entity->$getField();

                        $row[] = $this->formatDataForCsv($field, $value);

                        continue;
                    }

                    $isField = 'is' . ucfirst($field);
                    if (method_exists($entity, $isField)) {
                        $row[] = ($entity->$isField() === true) ? 'Yes' : 'No';
                    }
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $this->getExportName()));

        return $response;
    }

    /**
     * Format data for CSV
     */
    public function formatDataForCsv($field, $value)
    {
        return $value;
    }

    /**
     * Get entity class.
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Get export name.
     */
    public function getExportName()
    {
        return vsprintf('export-%s-%s.csv', [strtolower($this->getUserFriendlyEntityName()), date("YmdHis")]);
    }

    /**
     * Get edit form options.
     */
    public function getEditFormOptions()
    {
        return array();
    }

    /**
     * Get create form options.
     */
    public function getCreateFormOptions()
    {
        return array();
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
                return $this->isGranted($this->getGrantAttribute(EasyAdminVoterInterface::CREATE), $object);
            case 'show':
            case 'list':
            case 'csv':
                return $this->isGranted($this->getGrantAttribute(EasyAdminVoterInterface::SHOW), $object);
            case 'edit':
                return $this->isGranted($this->getGrantAttribute(EasyAdminVoterInterface::EDIT), $object);
            case 'delete':
                return $this->isGranted($this->getGrantAttribute(EasyAdminVoterInterface::DELETE), $object);
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
     * prependBreadcrumb
     *
     * @return array
     */
    public function prependAndAddBreadcrumbItem($item)
    {
        if ($this->has('white_october_breadcrumbs') === false) {
            return false;
        }

        $this->prependBreadcrumb();
        $this->get("white_october_breadcrumbs")->addItem($item);
    }

    /**
     * prependBreadcrumb
     *
     * @return array
     */
    public function prependBreadcrumb()
    {
        if ($this->has('white_october_breadcrumbs') === false) {
            return false;
        }

        $this->get("white_october_breadcrumbs")->addItem(
            $this->getMenuLabel('list'),
            $this->get("router")->generate($this->getCrudRoute('list'))
        );
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
     * Gets the menu label that are displayed in the menu.
     */
    public function getMenuLabel($type)
    {
        return ucfirst($type) . ' ' . $this->getUserFriendlyEntityName();
    }

    /**
     * Gets the page title that are displayed in the list/create pages.
     */
    public function getPageTitle($type)
    {
        return $this->getMenuLabel($type);
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
        if ($this->isGranted($this->getGrantAttribute(EasyAdminVoterInterface::DELETE), $entity) === true) {
            return $this->createFormBuilder(null, ['attr' => ['style' => 'display: inline']])
                ->setAction($this->generateUrl($this->getCrudRoute('delete'), $this->getCurrentRouteParameters($request)))
                ->setMethod('DELETE')
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
            if (in_array($routeKey, ['create', 'edit', 'show', 'list', 'csv']) === false) {
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
     * Gets the grant attribute specified in the configuration.
     *
     * @return string
     */
    public function getGrantAttribute($type)
    {
        switch ($type) {
            case EasyAdminVoterInterface::CREATE:
                return $this->getParameter('madrak_io_easy_admin.grants.attributes.create');
            case EasyAdminVoterInterface::SHOW:
                return $this->getParameter('madrak_io_easy_admin.grants.attributes.show');
            case EasyAdminVoterInterface::EDIT:
                return $this->getParameter('madrak_io_easy_admin.grants.attributes.edit');
            case EasyAdminVoterInterface::DELETE:
                return $this->getParameter('madrak_io_easy_admin.grants.attributes.delete');
            case EasyAdminVoterInterface::MENU:
                return $this->getParameter('madrak_io_easy_admin.grants.attributes.menu');
        }

        throw new Exception(vsprintf(self::UNEXPECTED_GRANT_ATTRIBUTE, [$type]));
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
                    'check_grants' => $this->getParameter('madrak_io_easy_admin.grants.check'),
                    'grants_attributes_create' => $this->getParameter('madrak_io_easy_admin.grants.attributes.create'),
                    'grants_attributes_show' => $this->getParameter('madrak_io_easy_admin.grants.attributes.show'),
                    'grants_attributes_edit' => $this->getParameter('madrak_io_easy_admin.grants.attributes.edit'),
                    'grants_attributes_delete' => $this->getParameter('madrak_io_easy_admin.grants.attributes.delete'),
                    'grants_attributes_menu' => $this->getParameter('madrak_io_easy_admin.grants.attributes.menu'),
                    'can_create' => $this->hasCrudRouteAccess('create'),
                    'has_alerts_enabled' => $this->hasAlertsEnabled()
                ];
    }
}
