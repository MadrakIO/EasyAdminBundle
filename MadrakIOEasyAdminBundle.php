<?php

namespace MadrakIO\Bundle\EasyAdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use MadrakIO\Bundle\EasyAdminBundle\DependencyInjection\FieldTypeCompilerPass;
use MadrakIO\Bundle\EasyAdminBundle\DependencyInjection\CrudControllerCompilerPass;

class MadrakIOEasyAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FieldTypeCompilerPass());
        $container->addCompilerPass(new CrudControllerCompilerPass());
    }
}
