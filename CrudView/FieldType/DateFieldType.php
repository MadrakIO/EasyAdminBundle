<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView\FieldType;

use \DateTime;

class DateFieldType extends AbstractFieldType
{
    public static function getListView()
    {
        return 'MadrakIOEasyAdminBundle:List:date.html.twig';
    }
    
    public static function getShowView()
    {
        return 'MadrakIOEasyAdminBundle:Show:date.html.twig';
    }
        
    public static function getName()
    {
        return 'date';
    }

    public function guess($data)
    {
        return ($data instanceOf DateTime);
    }
}