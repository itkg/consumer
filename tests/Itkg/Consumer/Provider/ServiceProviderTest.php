<?php

namespace Itkg\Consumer\Provider;

use Itkg\Core\Provider\ServiceProvider as CoreProvider;
use Itkg\Consumer\Provider\ServiceProvider as ConsumerProvider;
use Itkg\Core\ServiceContainer;

/**
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $container = new ServiceContainer();
        $container->register(new CoreProvider($container));
        $container->register(new ConsumerProvider($container));

        $this->assertArrayHasKey('consumer', $container);
    }
}
