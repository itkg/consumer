<?php

namespace Itkg\Consumer\Listener;

use Itkg\Consumer\Response;
use Itkg\Consumer\Service\Service;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class DeserializedListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testDeserializerAndDefaultDeserialization()
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new DeserializerListener(SerializerBuilder::create()->build()));

        $clientMock = $this->getMockBuilder('Itkg\Consumer\Client\RestClient')->getMock();

        $service = new Service(
            $eventDispatcher,
            $clientMock,
            array(
                'identifier' => 'loggable service',
            )
        );

        $service->sendRequest(Request::create('/'), new Response('[{"title":"value"}]'));

        $this->assertEquals(array(0 => array('title' => 'value')), $service->getResponse()->getDeserializedContent());

    }
} 