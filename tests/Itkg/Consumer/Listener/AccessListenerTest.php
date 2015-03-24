<?php

namespace Itkg\Consumer\Listener;

use Itkg\Consumer\Service\Service;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class AccessListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Itkg\Consumer\Exception\DisabledServiceException
     */
    public function testAccessServiceDisabled()
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new AccessListener());
        $clientMock = $this->getMockBuilder('Itkg\Consumer\Client\RestClient')->getMock();
        $clientMock->expects($this->never())->method('sendRequest');
        $service = new Service(
            $eventDispatcher,
            $clientMock,
            array(
                'identifier' => 'authenticable service',
                'disabled'   => true
            )
        );

        $service->sendRequest(Request::create('/'));
    }
}
