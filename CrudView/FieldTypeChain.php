<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView;

use MadrakIO\Bundle\EasyAdminBundle\CrudView\FieldType\AbstractFieldType;

class FieldTypeChain
{
    private $fieldTypes;

    public function __construct()
    {
        $this->fieldTypes = [];
    }

    public function addFieldType(AbstractFieldType $fieldType)
    {
        $this->fieldTypes[] = $fieldType;
    }
    
    public function getFieldTypes()
    {
        return $this->fieldTypes;
    }
}
