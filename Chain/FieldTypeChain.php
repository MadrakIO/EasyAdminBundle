<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Chain;

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
        
        return $this;
    }
    
    public function getFieldTypes()
    {
        return $this->fieldTypes;
    }
}
