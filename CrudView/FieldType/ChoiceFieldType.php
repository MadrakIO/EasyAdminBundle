<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView\FieldType;

class ChoiceFieldType extends AbstractFieldType
{
    public static function getListView()
    {
        return 'MadrakIOEasyAdminBundle:List:choice.html.twig';
    }

    public static function getShowView()
    {
        return 'MadrakIOEasyAdminBundle:Show:choice.html.twig';
    }

    public static function getName()
    {
        return 'choice';
    }

    public function guess($data)
    {
        return false;
    }
}
