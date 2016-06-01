<?php

namespace MadrakIO\Bundle\EasyAdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('madrak_io_easy_admin');

        $rootNode
            ->children()
                ->scalarNode('parent_template')
                    ->info('The twig template that should be extended by the CRUD views.')
                    ->cannotBeEmpty()
                ->end()
                ->booleanNode('check_grants')
                    ->info('If this is set to true, the AbstractCoreCRUDController will use denyAccessUnlessGranted to control access.')
                    ->defaultValue(false)
                ->end()
                ->booleanNode('display_ras_alerts')
                    ->info('If this is set to true and RasFlashAlertBundle is installed the AbstractCoreCRUDController will display success and error alerts using RasFlashAlertBundle.')
                    ->defaultValue(true)
            ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
