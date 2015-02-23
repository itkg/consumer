<?php

namespace Itkg\Consumer\Listener;

use Itkg\Consumer\Service\Service;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class LoggerListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testServiceSuccessAndFail()
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new LoggerListener());

        $clientMock = $this->getMockBuilder('Itkg\Consumer\Client\RestClient')->getMock();
        $loggerMock = $this->getMockBuilder('Psr\Log\AbstractLogger')->disableOriginalConstructor()->getMock();
        $loggerMock->expects($this->exactly(3))->method('info');
        $loggerMock->expects($this->once())->method('error');
        $loggableService = new Service(
            $eventDispatcher,
            $clientMock,
            array(
                'identifier' => 'loggable service',
                'loggable'   => true,
                'logger'     => $loggerMock
            )
        );

        $loggableService->sendRequest(Request::create('/'));

        $clientMock->expects($this->once())->method('sendRequest')->will($this->throwException(new \Exception('KO')));

        $loggableService->setClient($clientMock);

        try {
            $loggableService->sendRequest(Request::create('/'));
        } catch(\Exception $e) {
            $this->assertEquals($e, $loggableService->getException());
        }
    }
} 