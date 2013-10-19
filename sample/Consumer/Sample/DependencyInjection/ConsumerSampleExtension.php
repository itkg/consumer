<?php

namespace Consumer\Sample\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;

/**
 * Class ConsumerSampleExtension
 *
 * @author Pascal DENIS <pascal.denis.75@gmail.com>
 */
class ConsumerSampleExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/..//Resources/config')
        );

        $loader->load('google/translate.xml');
        $loader->load('google/shortener.xml');
    }

    public function getAlias()
    {
        return 'consumer_sample';
    }
}