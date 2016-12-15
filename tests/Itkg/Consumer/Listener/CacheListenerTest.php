<?php

namespace Itkg\Consumer\Listener;

use Itkg\Consumer\Service\Service;
use Itkg\Core\Cache\Adapter\Registry;
use Itkg\Core\Cache\CacheableData;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Itkg\Consumer\Response;

class CacheListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSetCache()
    {
        $registry = new Registry();
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new CacheListener($eventDispatcher));
        $clientMock = $this->getMockBuilder('Itkg\Consumer\Client\RestClient')->getMock();
        $clientMock->expects($this->once())->method('sendRequest');
        $cacheableService = new Service(
            $eventDispatcher,
            $clientMock,
            array(
                'identifier' => 'cacheable service',
                'cache_adapter' => $registry
            )
        );

        $this->assertFalse($cacheableService->isLoaded());
        $cacheableService->sendRequest(Request::create('/'));

        $this->assertNotNull($registry->get(new CacheableData($cacheableService->getHashKey(), null, array())));

        $cacheableService->sendRequest(Request::create('/'));
        $this->assertTrue($cacheableService->isLoaded());
    }
} 
