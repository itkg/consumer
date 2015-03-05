<?php

namespace Itkg\Consumer\Provider;

use Itkg\Consumer\Listener\CacheListener;
use Itkg\Consumer\Listener\DeserializerListener;
use Itkg\Consumer\Listener\LoggerListener;
use Itkg\Core\Provider\ServiceProviderInterface;
use JMS\Serializer\SerializerBuilder;

/**
 * Class ServiceProvider
 *
 * A provider for Core container
 *
 * @package Itkg\Consumer\Provider
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param \Pimple $mainContainer
     */
    public function register(\Pimple $mainContainer)
    {
        $container = new \Pimple();

        $container['deserializer_listener'] = $mainContainer->share(
            function () {
                return new DeserializerListener(
                    SerializerBuilder::create()->build()
                );
            }
        );

        $container['logger_listener'] = $mainContainer->share(
            function () {
                return new LoggerListener();
            }
        );

        $container['cache_listener'] = $mainContainer->share(
            function () use ($mainContainer) {
                return new CacheListener($mainContainer['core']['dispatcher']);
            }
        );

        $mainContainer['core']['dispatcher']->addSubscriber($container['cache_listener']);
        $mainContainer['core']['dispatcher']->addSubscriber($container['deserializer_listener']);
        $mainContainer['core']['dispatcher']->addSubscriber($container['logger_listener']);

        $mainContainer['consumer'] = $container;
    }
}
