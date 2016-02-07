<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView\Guesser;

use \Exception;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\FieldTypeChain;

class FieldTypeGuesser
{
    const GUESS_ERROR = 'Could not guess field type for field `%s`.';
    
    protected $fieldTypeChain;
    
    public function __construct(FieldTypeChain $fieldTypeChain)
    {
        $this->fieldTypeChain = $fieldTypeChain;
    }
    
    public function attemptGuess($field, $data)
    {
        foreach ($this->fieldTypeChain->getFieldTypes() AS $type)
        {
            if ($type::guess($data) === true) {
                return $type;
            }
        }
        
        throw new Exception(vsprintf(self::GUESS_ERROR, [$field]));
    }
}