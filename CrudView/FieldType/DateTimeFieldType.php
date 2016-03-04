<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView\FieldType;

use DateTime;

class DateTimeFieldType extends AbstractFieldType
{
    public static function getListView()
    {
        return 'MadrakIOEasyAdminBundle:List:datetime.html.twig';
    }

    public static function getShowView()
    {
        return 'MadrakIOEasyAdminBundle:Show:datetime.html.twig';
    }

    public static function getName()
    {
        return 'datetime';
    }

    public function guess($data)
    {
        return $data instanceof DateTime;
    }
}
