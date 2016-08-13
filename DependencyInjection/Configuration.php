<?php

namespace MadrakIO\Bundle\EasyAdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use MadrakIO\Bundle\EasyAdminBundle\Security\EasyAdminVoterInterface;

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
                ->arrayNode('grants')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('check')
                            ->defaultValue(false)
                            ->info('If this is set to true, the AbstractCoreCRUDController will use denyAccessUnlessGranted to control access.')
                            ->defaultValue(false)
                        ->end()
                        ->arrayNode('attributes')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('create')
                                    ->info('The attribute used for checking permissions to create an entity.')
                                    ->cannotBeEmpty()
                                    ->defaultValue(EasyAdminVoterInterface::CREATE)
                                ->end()
                                ->scalarNode('show')
                                    ->info('The attribute used for checking permissions to view an entity.')
                                    ->cannotBeEmpty()
                                    ->defaultValue(EasyAdminVoterInterface::SHOW)
                                ->end()
                                ->scalarNode('edit')
                                    ->info('The attribute used for checking permissions to edit an entity.')
                                    ->cannotBeEmpty()
                                    ->defaultValue(EasyAdminVoterInterface::EDIT)
                                ->end()
                                ->scalarNode('delete')
                                    ->info('The attribute used for checking permissions to delete an entity.')
                                    ->cannotBeEmpty()
                                    ->defaultValue(EasyAdminVoterInterface::DELETE)
                                ->end()
                                ->scalarNode('menu')
                                    ->info('The attribute used for checking permissions to see the menu option for an entity.')
                                    ->cannotBeEmpty()
                                    ->defaultValue(EasyAdminVoterInterface::MENU)
                                ->end()
                            ->end()
                        ->end()
                    ->end()
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
