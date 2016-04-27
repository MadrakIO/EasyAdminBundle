<?php

namespace MadrakIO\Bundle\EasyAdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class MadrakIOEasyAdminExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config as $configKey => $configValue) {
            $container->setParameter('madrak_io_easy_admin.'.$configKey, $configValue);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('fieldtype.services.yml');

        if (class_exists('Knp\Bundle\MenuBundle\KnpMenuBundle') === true) {
            $loader->load('menu.services.yml');
        }
    }

    public function getAlias()
    {
        return 'madrak_io_easy_admin';
    }
}
