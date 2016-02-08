<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use MadrakIO\Bundle\EasyAdminBundle\EntityManager\EntityManagerInterface;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\Guesser\FieldTypeGuesser;

abstract class AbstractListType extends AbstractType
{  
    protected $paginator;

    public function setPaginator($paginator)
    {
        $this->paginator = $paginator;
    }
    
    public function hasPaginator()
    {
        return ($this->paginator instanceOf \Knp\Component\Pager\Paginator);
    }

    public function createView(Request $request)
    {
        $this->build();
        $data = $this->getDataList($request);

        return $this->templating->render($this->getListWrapperView(), ['crud_list_data_header' => $this->fields, 'crud_list_data_rows' => $data]);
    }

    private function getDataList(Request $request)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('entity')
                     ->from($this->entityClass, 'entity');

        if ($this->hasPaginator() === true) {
            $pagination = $this->paginator->paginate($queryBuilder, $request->query->getInt('page', 1), $request->query->getInt('limit', 10));                        
                       
            $pagination->setItems($this->getData($pagination->getItems()));
            
            return $pagination;
        }

        
        return $this->getData($queryBuilder->getQuery()->getResult());
    }
    
    private function getData($results)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        
        foreach ($results AS &$result) {
            $rowData = [];
            foreach ($this->fields AS $field => &$options) {
                $currentFieldData = ($accessor->isReadable($result, $field) === true) ? $accessor->getValue($result, $field) : null;

                if (empty($options['type']) === true) {
                    $options['type'] = $this->fieldTypeGuesser->attemptGuess($field, $currentFieldData);
                    $options = $options['type']::getDefaultOptions($options, $field);
                }
                
                $rowData[] = $options['type']::getDefaultOptions($options + ['data' => $currentFieldData], $field, $result);
            }            
            
            $result = $rowData;
        }
                
        return $results;
    }
    
    private function getListWrapperView()
    {
        if ($this->hasPaginator() === true) {
            return 'MadrakIOEasyAdminBundle:List:Layout/wrapper_pagination.html.twig';
        }
        
        return 'MadrakIOEasyAdminBundle:List:Layout/wrapper.html.twig';
    }
}