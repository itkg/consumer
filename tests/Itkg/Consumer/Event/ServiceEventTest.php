<?php

namespace Itkg\Consumer\Event;

use Itkg\Consumer\Client\RestClient;
use Itkg\Consumer\Service\SimpleService;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ServiceEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $service = $this->getMockBuilder('Itkg\Consumer\Service\SimpleService')->disableOriginalConstructor()->getMock();
        $event = new ServiceEvent($service);

        $this->assertEquals($event->getService(), $service);
    }
}
