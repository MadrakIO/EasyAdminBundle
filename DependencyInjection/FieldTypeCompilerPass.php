<?php

namespace MadrakIO\Bundle\EasyAdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class FieldTypeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('madrak_io_easy_admin.field_type_chain')) {
            return;
        }

        $definition = $container->findDefinition(
            'madrak_io_easy_admin.field_type_chain'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'madrak_io_easy_admin.field_type'
        );
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addFieldType',
                array(new Reference($id))
            );
        }
    }
}
