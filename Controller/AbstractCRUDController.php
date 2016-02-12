<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\AbstractShowType;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\AbstractListType;

abstract class AbstractCRUDController extends Controller
{
    protected $router;
    protected $entityManager;
    protected $entityFormType;
    protected $entityList;
    protected $entityShow;
    protected $entityClass;
    
    public function __construct(Router $router, EntityManagerInterface $entityManager, AbstractType $entityFormType, AbstractListType $entityList, AbstractShowType $entityShow, $entityClass)
    {
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->entityFormType = $entityFormType;
        $this->entityList = $entityList;
        $this->entityShow = $entityShow;
        $this->entityClass = $entityClass;
    }

    /**
     * Lists all entities.
     *
     * @Route("/")
     * @Method("GET")     
     */
    public function listAction(Request $request)
    {
        return $this->render('MadrakIOEasyAdminBundle:CRUD:list.html.twig', array(
            'parent_template' => $this->getParameter('madrak_io_easy_admin.parent_template'),
            'current_route' => $this->getCurrentRouteName($request),                        
            'routes' => $this->getRelatedCRUDRoutes($request),            
            'listView' => $this->entityList->createView($request),
        ));
    }

    /**
     * Creates a new entity.
     *
     * @Route("/create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request)
    {
        $entity = new $this->entityClass;
        $form = $this->createForm($this->entityFormType, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return $this->redirectToRoute($this->getRelatedCRUDRoute($request, 'show'), array('id' => $entity->getId()));
        }

        return $this->render('MadrakIOEasyAdminBundle:CRUD:create.html.twig', array(
            'parent_template' => $this->getParameter('madrak_io_easy_admin.parent_template'),
            'current_route' => $this->getCurrentRouteName($request),            
            'routes' => $this->getRelatedCRUDRoutes($request),
            'entity' => $entity,
            'form' => $form->createView(),
        ));
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
        $deleteForm = $this->createDeleteForm($request, $entity);

        return $this->render('MadrakIOEasyAdminBundle:CRUD:show.html.twig', array(
            'parent_template' => $this->getParameter('madrak_io_easy_admin.parent_template'),
            'current_route' => $this->getCurrentRouteName($request),            
            'routes' => $this->getRelatedCRUDRoutes($request),
            'entity' => $entity,            
            'showView' => $this->entityShow->createView($entity),
            'delete_form' => $deleteForm->createView(),
        ));
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
        $deleteForm = $this->createDeleteForm($request, $entity);
        $editForm = $this->createForm($this->entityFormType, $entity);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return $this->redirectToRoute($this->getRelatedCRUDRoute($request, 'edit'), array('id' => $entity->getId()));
        }

        return $this->render('MadrakIOEasyAdminBundle:CRUD:edit.html.twig', array(
            'parent_template' => $this->getParameter('madrak_io_easy_admin.parent_template'),       
            'current_route' => $this->getCurrentRouteName($request),            
            'routes' => $this->getRelatedCRUDRoutes($request),            
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
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
        $form = $this->createDeleteForm($request, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute($this->getRelatedCRUDRoute($request, 'list'));
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
        return $this->createFormBuilder(null, ['attr' => ['style' => 'display: inline']])
            ->setAction($this->generateUrl($this->getRelatedCRUDRoute($request, 'delete'), array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Gets a specific related route based on the current route
     */    
    protected function getRelatedCRUDRoute(Request $request, $routeType)
    {
        return $this->getRelatedCRUDRoutes($request)[$routeType];
    }
    
    /**
     * Gets related routes based on current route
     */
    protected function getRelatedCRUDRoutes(Request $request)
    {
        $rootRouteName = str_replace(['_list', '_create', '_show', '_edit', '_delete'], '', $this->getCurrentRouteName($request));
        
        return [
                    'list' => $rootRouteName . '_list',
                    'create' => $rootRouteName . '_create',
                    'show' => $rootRouteName . '_show',
                    'edit' => $rootRouteName . '_edit',
                    'delete' => $rootRouteName . '_delete',
                ];
    }

    /**
     * Gets the current route
     */    
    protected function getCurrentRouteName(Request $request)
    {
        return $this->router->matchRequest($request)['_route'];
    }
}

