<?php

namespace Itkg\Consume\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;

/**
 * Class ItkgConsumeExtension
 *
 * @author Pascal DENIS <pascal.denis.75@gmail.com>
 */
class ItkgConsumeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../../../Resources/config')
        );

        $loader->load('client.xml');
        $loader->load('service.xml');
        $loader->load('method.xml');
        $loader->load('hydrator.xml');
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'itkg_consume';
    }
}