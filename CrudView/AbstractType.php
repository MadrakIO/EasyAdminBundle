<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\Guesser\FieldTypeGuesser;

abstract class AbstractType
{
    protected $fields;
    protected $templating;
    protected $entityManager;
    protected $fieldTypeGuesser;
    protected $entityClass;

    abstract public function build();
    
    public function __construct(EngineInterface $templating, EntityManagerInterface $entityManager, FieldTypeGuesser $fieldTypeGuesser, $entityClass)
    {
        $this->templating = $templating;
        $this->entityManager = $entityManager;
        $this->fieldTypeGuesser = $fieldTypeGuesser;
        $this->entityClass = $entityClass;
    }
            
    public function add($field, $type = null, array $options = [])
    {
        $this->fields[$field] = $options + ['type' => $type];
        
        return $this;
    }
}