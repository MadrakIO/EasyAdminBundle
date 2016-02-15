<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView;

abstract class AbstractType
{
    protected $fields;
    protected $templating;
    protected $entityManager;
    protected $fieldTypeGuesser;
    protected $entityClass;

    abstract public function build();
                
    public function add($field, $type = null, array $options = [])
    {
        $this->fields[$field] = $options + ['type' => $type];
        
        return $this;
    }
}