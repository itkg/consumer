<?php

namespace Consume\Sample\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;

/**
 * Class ConsumeSampleExtension
 *
 * @author Pascal DENIS <pascal.denis.75@gmail.com>
 */
class ConsumeSampleExtension extends Extension
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
        return 'consume_sample';
    }
}