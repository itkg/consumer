<?php

namespace Itkg\Consumer\Listener;

use Itkg\Consumer\Service\CacheableService;
use Itkg\Core\Cache\Adapter\Registry;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Itkg\Consumer\Response;

class CacheListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSetCache()
    {
        $registry = new Registry();
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(new CacheListener($registry, $eventDispatcher));
        $clientMock = $this->getMockBuilder('Itkg\Consumer\Client\RestClient')->getMock();
        $clientMock->expects($this->once())->method('sendRequest');
        $cacheableService = new CacheableService(
            $eventDispatcher,
            $clientMock,
            array(
                'identifier' => 'cacheable service',
                'cache_serializer' => function (Response $response) {
                    $response->setContent('My content');
                    return serialize($response);
                },
            )
        );

        $this->assertFalse($cacheableService->isLoaded());
        $cacheableService->sendRequest(Request::create('/'));

        $this->assertNotNull($registry->get($cacheableService));

        $cacheableService->sendRequest(Request::create('/'));
        $this->assertTrue($cacheableService->isLoaded());
    }
} 