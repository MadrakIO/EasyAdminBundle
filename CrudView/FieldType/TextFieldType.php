<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView\FieldType;

class TextFieldType extends AbstractFieldType
{
    public static function getName()
    {
        return 'text';
    }

    public function guess($data)
    {
        return is_string($data) === true || empty($data) === true;
    }
}
