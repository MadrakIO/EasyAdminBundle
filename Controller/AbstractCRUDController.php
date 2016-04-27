<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Controller;

use Symfony\Component\Form\AbstractType;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\AbstractShowType;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\AbstractListType;

abstract class AbstractCRUDController extends AbstractSimpleCRUDController
{
    public function __construct(AbstractType $entityFormType, AbstractListType $entityList, AbstractShowType $entityShow, $entityClass)
    {
        parent::__construct($entityFormType, $entityList, $entityShow, $entityClass);

        @trigger_error('The AbstractCRUDController class is deprecated. You should extend the AbstractSimpleCRUDController instead.', E_USER_DEPRECATED);
    }
}
