<?php

namespace Itkg\Consumer\Event;

use Itkg\Consumer\Client\RestClient;
use Itkg\Consumer\Service\LightService;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ServiceEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $service = $this->getMockBuilder('Itkg\Consumer\Service\LightService')->disableOriginalConstructor()->getMock();
        $event = new ServiceEvent($service);

        $this->assertEquals($event->getService(), $service);
    }
}
