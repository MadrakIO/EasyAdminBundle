<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView\FieldType;

use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractFieldType
{
    public static function getListView()
    {
        return 'MadrakIOEasyAdminBundle:List:default.html.twig';
    }

    public static function getShowView()
    {
        return 'MadrakIOEasyAdminBundle:Show:default.html.twig';
    }
    
    public static function getName()
    {
        return null;
    }
    
    public static function getDefaultOptions(array $options, $field, $entity = null)
    {
        if (isset($field) === true && isset($options['label']) === false) {
            $options['label'] = ucwords(str_replace('.', ' ', $field));
        }

        if (isset($field, $entity) === true && isset($options['data']) === false) {
            $options['data'] = static::getData($field, $entity);
        }
        
        return $options;
    }
    
    public static function getData($field, $entity)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        return ($accessor->isReadable($entity, $field) === true) ? $accessor->getValue($entity, $field) : null;
    }
    
    public function guess($data)
    {
        return false;
    }
    
    public function isSortable()
    {
        return true;
    }
}