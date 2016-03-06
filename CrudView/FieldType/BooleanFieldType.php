<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView\FieldType;

class BooleanFieldType extends AbstractFieldType
{
    public static function getListView()
    {
        return 'MadrakIOEasyAdminBundle:List:boolean.html.twig';
    }

    public static function getShowView()
    {
        return 'MadrakIOEasyAdminBundle:Show:boolean.html.twig';
    }

    public static function getName()
    {
        return 'boolean';
    }

    public function guess($data)
    {
        return is_bool($data);
    }
}
