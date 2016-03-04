<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView\FieldType;

class ButtonFieldType extends AbstractFieldType
{
    public static function getListView()
    {
        return 'MadrakIOEasyAdminBundle:List:button.html.twig';
    }

    public static function getShowView()
    {
        return 'MadrakIOEasyAdminBundle:Show:button.html.twig';
    }

    public static function getName()
    {
        return 'button';
    }

    public static function getDefaultOptions(array $options, $field, $entity = null)
    {
        $options = parent::getDefaultOptions($options, $field, $entity);

        if (isset($entity) === true) {
            if (isset($options['route'], $options['route']['parameters']) === true) {
                foreach ($options['route']['parameters'] as $parameterKey => &$parameterField) {
                    $parameterField = self::getData($parameterField, $entity);
                }
            } else {
                $options['route']['parameters']['id'] = self::getData('id', $entity);
            }
        }

        return $options;
    }

    public function guess($data)
    {
        return false;
    }

    public function isSortable()
    {
        return false;
    }
}
