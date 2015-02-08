<?php

namespace Itkg\Consumer\Event;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSetAdd()
    {
        $options = array('identifier' => 'my identifier');
        $resolver = new OptionsResolver();
        $event = new ConfigEvent($resolver, $options);

        $this->assertEquals($options, $event->getOptions());
        $this->assertEquals($resolver, $event->getOptionsResolver());
        $this->assertEquals($event, $event->setOptions(array('key' => 'value')));
        $this->assertArrayHasKey('key', $event->getOptions());
        $event->addOption('key', 'value2');
        $options = $event->getOptions();
        $this->assertEquals('value2', $options['key']);
    }
} 