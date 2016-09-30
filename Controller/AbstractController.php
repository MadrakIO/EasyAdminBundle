<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Controller;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Routing\AnnotatedRouteControllerLoader;

abstract class AbstractController extends Controller
{
    protected $entityManager;
    protected $menuGroup;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->entityManager = $container->get('doctrine.orm.default_entity_manager');
    }

    /**
     * Gets the current route.
     *
     * @return string
     */
    protected function getCurrentRoute(Request $request)
    {
        return $this->get('router')->matchRequest($request);
    }

    /**
     * Gets the current route name.
     *
     * @return string
     */
    protected function getCurrentRouteName(Request $request)
    {
        return $this->getCurrentRoute($request)['_route'];
    }

    /**
     * Gets the current route parameters.
     *
     * @return array
     */
    protected function getCurrentRouteParameters(Request $request)
    {
        $routeDetails = $this->getCurrentRoute($request);

        $parameters = [];
        foreach ($routeDetails AS $routeDetailKey => $routeDetail) {
            if (in_array($routeDetailKey, ['_route', '_controller']) === true) {
                continue;
            }

            $parameters[$routeDetailKey] = $routeDetail;
        }

        return $parameters;
    }

    /**
     * Gets the current route parameters.
     *
     * @return array
     */
     protected function getCreateRouteRedirect(Request $request, $entity)
     {
         return ['route' => $this->getCrudRoute('show'), 'parameters' => ['id' => $entity->getId()]];
     }

    /**
     * Gets all routes for controller.
     *
     * @return array
     */
    public function getRelatedRoutes()
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
     * {@inheritdoc}
     */
    public function getMenuGroup()
    {
        return $this->menuGroup;
    }

    /**
     * Displays a Success Alert if display_ras_alerts is enabled
     *
     * @return AbstractController
     */
    protected function alertSuccess($message, array $parameters = [])
    {
        if ($this->hasAlertsEnabled() === true) {
            $this->get('ras_flash_alert.alert_reporter')->addSuccess(vsprintf($message, $parameters));
        }

        return $this;
    }

    /**
     * Displays an Error Alert if display_ras_alerts is enabled
     *
     * @return AbstractController
     */
    protected function alertError($message, array $parameters = [])
    {
        if ($this->hasAlertsEnabled() === true) {
            $this->get('ras_flash_alert.alert_reporter')->addError(vsprintf($message, $parameters));
        }

        return $this;
    }

    /**
     * Displays a Warning Alert if display_ras_alerts is enabled
     *
     * @return AbstractController
     */
    protected function alertWarning($message, array $parameters = [])
    {
        if ($this->hasAlertsEnabled() === true) {
            $this->get('ras_flash_alert.alert_reporter')->addWarning(vsprintf($message, $parameters));
        }

        return $this;
    }

    /**
     * Displays an Info Alert if display_ras_alerts is enabled
     *
     * @return AbstractController
     */
    protected function alertInfo($message, array $parameters = [])
    {
        if ($this->hasAlertsEnabled() === true) {
            $this->get('ras_flash_alert.alert_reporter')->addInfo(vsprintf($message, $parameters));
        }

        return $this;
    }

    /**
     * Returns true if alerts should be displayed, false if they should not
     *
     * @return boolean
     */
    protected function hasAlertsEnabled()
    {
        return $this->getParameter('madrak_io_easy_admin.display_ras_alerts') === true && $this->container->has('ras_flash_alert.alert_reporter') === true;
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    protected function isGranted($attributes, $object = null)
    {
        if ($this->getParameter('madrak_io_easy_admin.grants.check') === true) {
            return parent::isGranted($attributes, $object);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    protected function denyAccessUnlessGranted($attributes, $object = null, $message = 'Access Denied.')
    {
        if ($this->getParameter('madrak_io_easy_admin.grants.check') === true) {
            return parent::denyAccessUnlessGranted($attributes, $object, $message);
        }
    }
}
