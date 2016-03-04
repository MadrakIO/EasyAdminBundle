<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\HttpFoundation\Request;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\Guesser\FieldTypeGuesser;
use MadrakIO\Bundle\EasyAdminBundle\Security\EasyAdminVoterInterface;

abstract class AbstractListType extends AbstractType
{
    protected $entityClass;
    protected $paginator;
    protected $checkGrants = true;

    public function __construct(EngineInterface $templating, EntityManagerInterface $entityManager, AuthorizationChecker $authorizationChecker, FieldTypeGuesser $fieldTypeGuesser, $entityClass)
    {
        $this->templating = $templating;
        $this->entityManager = $entityManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->fieldTypeGuesser = $fieldTypeGuesser;
        $this->entityClass = $entityClass;
    }

    public function setCheckGrants($checkGrants)
    {
        $this->checkGrants = $checkGrants;
    }

    public function isGranted($attributes, $object = null)
    {
        if ($this->checkGrants === true) {
            return $this->authorizationChecker->isGranted($attributes, $object);
        }

        return true;
    }

    public function setPaginator($paginator)
    {
        $this->paginator = $paginator;
    }

    public function hasPaginator()
    {
        return $this->paginator instanceof \Knp\Component\Pager\Paginator;
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

        foreach ($results as &$result) {
            if ($this->isGranted(EasyAdminVoterInterface::SHOW, $result) === false) {
                $result = null;

                continue;
            }

            $rowData = [];
            foreach ($this->fields as $field => &$options) {
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
