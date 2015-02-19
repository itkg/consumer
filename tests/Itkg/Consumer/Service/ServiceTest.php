<?php

namespace Itkg\Consumer;

use Itkg\Consumer\Client\RestClient;
use Itkg\Consumer\Event\ServiceEvents;
use Itkg\Consumer\Service\Service;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "identifier" is missing
     */
    public function testIdentifierNotSet()
    {
        new Service(new EventDispatcher(), new RestClient());
    }

    public function testSendRequest()
    {
        $eventDispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')->getMock();
        $eventDispatcherMock->expects($this->at(2))->method('dispatch')->with(ServiceEvents::REQUEST);
        $eventDispatcherMock->expects($this->at(3))->method('dispatch')->with(ServiceEvents::RESPONSE);

        $clientMock = $this->getMockBuilder('Itkg\Consumer\Client\RestClient')->getMock();
        $clientMock->expects($this->once())->method('sendRequest');

        $service = new Service(
            $eventDispatcherMock,
            $clientMock,
            array('identifier' => 'My service')
        );
        $service->sendRequest(Request::createFromGlobals());
    }

    public function testSendRequestWithSettedResponse()
    {
        $eventDispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')->getMock();
        $eventDispatcherMock->expects($this->at(2))->method('dispatch')->with(ServiceEvents::REQUEST);
        $eventDispatcherMock->expects($this->exactly(4))->method('dispatch');

        $clientMock = $this->getMockBuilder('Itkg\Consumer\Client\RestClient')->getMock();
        $clientMock->expects($this->never())->method('sendRequest');

        $service = new Service(
            $eventDispatcherMock,
            $clientMock,
            array('identifier' => 'My service')
        );
        $service->sendRequest(Request::createFromGlobals(), new Response('With content'));
    }

    public function testConfigure()
    {
        $eventDispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')->getMock();
        $eventDispatcherMock->expects($this->at(0))->method('dispatch')->with(ServiceEvents::PRE_CONFIGURE);
        $eventDispatcherMock->expects($this->at(1))->method('dispatch')->with(ServiceEvents::POST_CONFIGURE);

        $options = array('identifier' => 'My service');
        $service = new Service($eventDispatcherMock, new RestClient(), $options);
        $this->assertEquals('My service', $service->getOption('identifier'));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testLoggerType()
    {
        $service = new Service(new EventDispatcher(), new RestClient(), array('identifier' => 'identifier', 'logger' => 1));
        $this->assertEquals('My service', $service->getOption('identifier'));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testCacheTtlType()
    {
        $service = new Service(new EventDispatcher(), new RestClient(), array('identifier' => 'identifier', 'cache_ttl' => 'test'));
        $this->assertEquals('My service', $service->getOption('identifier'));
    }

    public function testGetSet()
    {
        $client = new RestClient();
        $options = array('identifier' => 'my identifier');
        $service = new Service(new EventDispatcher(), $client, $options);

        $this->assertEquals($client, $service->getClient());
        $this->assertNull($service->getRequest());
        $this->assertNull($service->getResponse());

        $client = new RestClient(array('timeout' => 10));
        $request = Request::create('/');
        $response = new Response('a content');
        $options = array('identifier' => 'my new identifier');

        $this->assertEquals($service, $service->setClient($client));
        $this->assertEquals($service, $service->setRequest($request));
        $this->assertEquals($service, $service->setResponse($response));

        $this->assertEquals($client, $service->getClient());
        $this->assertEquals($request, $service->getRequest());
        $this->assertEquals($response, $service->getResponse());

    }
}
