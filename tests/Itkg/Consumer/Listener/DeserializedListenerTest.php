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

        $deserializerListener = new DeserializerListener(SerializerBuilder::create()->build());

        $eventDispatcher->addSubscriber($deserializerListener);

        $clientMock = $this->getMockBuilder('Itkg\Consumer\Client\RestClient')->getMock();

        $service = new Service(
            $eventDispatcher,
            $clientMock
        );

        $service->sendRequest(Request::create('/'), new Response('[{"title":"value"}]'));
        $this->assertEquals($deserializerListener, $deserializerListener->setSerializer(SerializerBuilder::create()->build()));
        $this->assertEquals(array(0 => array('title' => 'value')), $service->getResponse()->getDeserializedContent());

    }
} 