<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView\Labeler;

class FieldTypeLabeler
{
    public static function generateLabel($fieldName)
    {
        return preg_replace('/(?<=\\w)(?=[A-Z])/', ' $1', ucwords(str_replace('.', ' ', $fieldName)));
    }
}
