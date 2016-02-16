<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\Guesser\FieldTypeGuesser;

abstract class AbstractShowType extends AbstractType
{
    public function __construct(EngineInterface $templating, EntityManagerInterface $entityManager, FieldTypeGuesser $fieldTypeGuesser)
    {
        $this->templating = $templating;
        $this->entityManager = $entityManager;
        $this->fieldTypeGuesser = $fieldTypeGuesser;
    }
    
    public function createView($entity)
    {
        $this->build();
        $this->getData($entity);
        
        return $this->templating->render('MadrakIOEasyAdminBundle:Show:Layout/wrapper.html.twig', ['crud_show_data_rows' => $this->fields]);
    }
    
    private function getData($entity)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->fields AS $field => &$options) {
            if (isset($options['data']) === false) {
                if (isset($options['type']) === true) {
                    $options['data'] = $options['type']::getData($field, $entity);                
                } else {
                    $options['data'] = ($accessor->isReadable($entity, $field) === true) ? $accessor->getValue($entity, $field) : null;                    
                }
            }            

            if (empty($options['type']) === true) {
                $options['type'] = $this->fieldTypeGuesser->attemptGuess($field, $options['data']);
            }
            
            $options = $options['type']::getDefaultOptions($options + ['data' => $currentFieldData], $field, $entity);
        }

        return $this->fields;
    }
}