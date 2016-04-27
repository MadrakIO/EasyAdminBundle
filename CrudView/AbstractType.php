<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView;

use MadrakIO\Bundle\EasyAdminBundle\CrudView\Labeler\FieldTypeLabeler;

abstract class AbstractType
{
    protected $fields;
    protected $templating;
    protected $entityManager;
    protected $fieldTypeGuesser;

    abstract public function build();

    public function add($field, $type = null, array $options = [])
    {
        $this->fields[$field] = $this->generateOptions($field, $type, $options);

        return $this;
    }

    public function generateOptions($field, $type, array $options)
    {
        if (isset($options['type']) === false) {
            $options['type'] = $type;
        }

        if (isset($options['label']) === false) {
            $options['label'] = FieldTypeLabeler::generateLabel($field);
        }

        return $options;
    }
}
