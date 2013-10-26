<?php

namespace Itkg\Consumer\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;

/**
 * Class ItkgConsumerExtension
 *
 * @author Pascal DENIS <pascal.denis.75@gmail.com>
 */
class ItkgConsumerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../../../Resources/config')
        );

        $loader->load('client.xml');
        $loader->load('service.xml');
        $loader->load('hydrator.xml');
        $loader->load('event.xml');
        $loader->load('cache.xml');
    }
}