<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Controller;

interface DashboardAwareControllerInterface
{
    /*
     * Returns a multi-dimensional array with routes and page titles
     * These routes are then used in the Dashboard
     *
     * @return array
     */
    public function getDashboardRoutes();
}
