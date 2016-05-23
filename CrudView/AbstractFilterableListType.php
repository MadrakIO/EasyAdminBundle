<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\Labeler\FieldTypeLabeler;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractFilterableListType extends AbstractListType
{
    protected $filters = [];

    abstract public function configureFilters();

    public function createView(Request $request, array $criteria = [])
    {
        $this->build();
        $data = $this->getDataList($request, $criteria);
        $entity = new $this->entityClass();
        $filterForm = $this->createFilterForm($request, $entity);

        return $this->templating->render($this->getListWrapperView(), ['crud_list_data_header' => $this->fields, 'crud_list_data_rows' => $data, 'filter_form' => $filterForm->createView()]);
    }

    public function addFilter($filter, $type = null, array $options = [])
    {
        $this->filters[$filter] = $this->generateFilterOptions($filter, $type, $options);

        return $this;
    }

    public function generateFilterOptions($field, $type, array $options)
    {
        $options['required'] = false;
        $label = FieldTypeLabeler::generateLabel($field);

        return array('type' => $type, 'label' => $label, 'options' => $options);
    }

    protected function createFilterForm(Request $request, $entity)
    {
        $form = $this->formFactory->createBuilder(FormType::class)
            ->setMethod('GET');

        foreach ($this->filters as $key => $filterField) {
            $form->add($key, $filterField['type'], $filterField['options']);
        }

        return $form->getForm();
    }

    protected function createQueryBuilder(Request $request, array $criteria)
    {
        $params = $request->query->get('form');

        $queryBuilder = $this->entityManager->createQueryBuilder()
                                            ->select('entity')
                                            ->from($this->entityClass, 'entity');

        if (isset($params)) {
            foreach ($params as $param => $value) {
                if (empty($value) === false and $param != '_token') {
                    $queryBuilder->where(sprintf('entity.%s = :%s', $param, $param))
                                 ->setParameter($param, $value);
                }
            }
        }

        return $queryBuilder;
    }
}
