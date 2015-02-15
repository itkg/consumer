<?php

namespace Itkg\Consumer\Provider;

use Itkg\Consumer\Listener\DeserializerListener;
use Itkg\Consumer\Listener\LoggerListener;
use Itkg\Core\Provider\ServiceProviderInterface;
use JMS\Serializer\SerializerBuilder;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param \Pimple $container An Container instance
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

        $mainContainer['core']['dispatcher']->addSubscriber($container['deserializer_listener']);

        $mainContainer['consumer'] = $container;
    }
}