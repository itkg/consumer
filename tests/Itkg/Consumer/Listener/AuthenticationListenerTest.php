<?php

namespace Itkg\Consumer\Listener;

use Itkg\Consumer\Service\Service;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testAuthenticate()
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new AuthenticationListener());
        $clientMock = $this->getMockBuilder('Itkg\Consumer\Client\RestClient')->getMock();
        $authenticatorMock = $this->getMockBuilder('Itkg\Consumer\Authentication\AuthenticationProviderInterface')->getMock();
        $authenticatorMock->expects($this->once())->method('authenticate');
        $authenticatorMock->expects($this->exactly(2))->method('hydrate');

        $service = new Service(
            $eventDispatcher,
            $clientMock,
            array(
                'identifier' => 'authenticable service',
                'authentication_provider' => $authenticatorMock
            )
        );

        $service->sendRequest(Request::create('/'));
        $this->assertFalse($service->isAuthenticated());
        $authenticatorMock->expects($this->any())->method('getToken')->will($this->returnValue('MY_TOKEN'));
        $service->sendRequest(Request::create('/'));
        $this->assertTrue($service->isAuthenticated());
    }
} 