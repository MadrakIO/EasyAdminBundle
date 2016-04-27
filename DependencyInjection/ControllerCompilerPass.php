<?php

namespace MadrakIO\Bundle\EasyAdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ControllerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->has('madrak_io_easy_admin.controller_chain') === false) {
            return;
        }

        $definition = $container->findDefinition(
            'madrak_io_easy_admin.controller_chain'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'madrak_io_easy_admin.controller'
        );

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addController',
                array(new Reference($id))
            );
        }
    }
}
