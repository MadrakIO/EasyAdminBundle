<?php

namespace MadrakIO\Bundle\EasyAdminBundle\CrudView;

use \Twig_Environment;
use \Twig_SimpleFunction;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class CrudViewTwigExtension extends \Twig_Extension
{        
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('list_row', [$this, 'listRow'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new Twig_SimpleFunction('show_row', [$this, 'showRow'], ['needs_environment' => true, 'is_safe' => ['html']]),
        );
    }

    public function listRow(Twig_Environment $environment, array $field)
    {
        return $environment->render($field['type']::getListView(), $field);
    }

    public function showRow(Twig_Environment $environment, array $field)
    {
        return $environment->render($field['type']::getShowView(), $field);
    }
    
    public function getName()
    {
        return 'crud_view_twig_extension';
    }
}
