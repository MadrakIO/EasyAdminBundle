<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Controller;

interface CrudControllerInterface
{
    /**
     * Get entity class path.
     *
     * @return string
     */
    public function getEntityClass();

    /**
     * Get user friendly entity name.
     *
     * @return string
     */
    public function getUserFriendlyEntityName();
}
