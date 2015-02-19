<?php

namespace Itkg\Consumer\Event;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSetAdd()
    {
        $resolver = new OptionsResolver();
        $service = $this->getMockBuilder('Itkg\Consumer\Service\Service')->disableOriginalConstructor()->getMock();

        $event = new ConfigEvent($resolver, $service);

        $this->assertEquals($event->getService(), $service);
        $this->assertEquals($resolver, $event->getOptionsResolver());
    }
} 