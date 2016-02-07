<?php

namespace MadrakIO\Bundle\EasyAdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use MadrakIO\Bundle\EasyAdminBundle\DependencyInjection\FieldTypeCompilerPass;

class MadrakIOEasyAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FieldTypeCompilerPass());
    }
}
