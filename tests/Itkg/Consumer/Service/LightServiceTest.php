<?php

namespace Itkg\Consumer;

use Itkg\Consumer\Client\RestClient;
use Itkg\Consumer\Event\ServiceEvents;
use Itkg\Consumer\Service\LightService;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LightServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "identifier" is missing
     */
    public function testIdentifierNotSet()
    {
        new LightService(new EventDispatcher(), new RestClient());
    }

    public function testSendRequest()
    {
        $eventDispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')->getMock();
        $eventDispatcherMock->expects($this->at(2))->method('dispatch')->with(ServiceEvents::REQUEST);
        $eventDispatcherMock->expects($this->at(3))->method('dispatch')->with(ServiceEvents::RESPONSE);

        $clientMock = $this->getMockBuilder('Itkg\Consumer\Client\RestClient')->getMock();
        $clientMock->expects($this->once())->method('sendRequest');

        $service = new LightService(
            $eventDispatcherMock,
            $clientMock,
            Request::createFromGlobals(),
            new Response(),
            array('identifier' => 'My service')
        );
        $service->sendRequest();
    }

    public function testSendRequestWithSettedResponse()
    {
        $eventDispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')->getMock();
        $eventDispatcherMock->expects($this->at(2))->method('dispatch')->with(ServiceEvents::REQUEST);
        $eventDispatcherMock->expects($this->exactly(3))->method('dispatch');

        $clientMock = $this->getMockBuilder('Itkg\Consumer\Client\RestClient')->getMock();
        $clientMock->expects($this->never())->method('sendRequest');

        $service = new LightService(
            $eventDispatcherMock,
            $clientMock,
            Request::createFromGlobals(),
            new Response('Existing content'),
            array('identifier' => 'My service')
        );
        $service->sendRequest();
    }

    public function testConfigure()
    {
        $eventDispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')->getMock();
        $eventDispatcherMock->expects($this->at(0))->method('dispatch')->with(ServiceEvents::PRE_CONFIGURE);
        $eventDispatcherMock->expects($this->at(1))->method('dispatch')->with(ServiceEvents::POST_CONFIGURE);

        $options = array('identifier' => 'My service');
        $service = new LightService($eventDispatcherMock, new RestClient(), null, null, $options);
        $this->assertEquals($options, $service->getOptions());

    }

    public function testGetSet()
    {
        $client = new RestClient();
        $request = Request::createFromGlobals();
        $response = new Response();
        $options = array('identifier' => 'my identifier');
        $service = new LightService(new EventDispatcher(), $client, $request, $response, $options);

        $this->assertEquals($client, $service->getClient());
        $this->assertEquals($request, $service->getRequest());
        $this->assertEquals($response, $service->getResponse());
        $this->assertEquals($options, $service->getOptions());

        $client = new RestClient(array('timeout' => 10));
        $request = Request::create('/');
        $response = new Response('a content');
        $options = array('identifier' => 'my new identifier');

        $this->assertEquals($service, $service->setClient($client));
        $this->assertEquals($service, $service->setRequest($request));
        $this->assertEquals($service, $service->setResponse($response));
        $this->assertEquals($service, $service->setOptions($options));

        $this->assertEquals($client, $service->getClient());
        $this->assertEquals($request, $service->getRequest());
        $this->assertEquals($response, $service->getResponse());
        $this->assertEquals($options, $service->getOptions());

    }
}
