<?php

namespace Itkg\Consumer;

use Itkg\Consumer\Client\RestClient;
use Itkg\Consumer\Service\CacheableService;
use Itkg\Consumer\Service\ServiceCacheable;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheableServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConfiguration()
    {
        $service = new CacheableService(new EventDispatcher(), new RestClient(), array('identifier' => 'cacheable service'));
        $options = $service->getOptions();

        $this->assertNull($options['cache_ttl']);
        $this->assertEquals('serialize', $options['cache_serializer']);
        $this->assertEquals('unserialize', $options['cache_unserializer']);

    }

    public function testConfigure()
    {
        $options = array(
            'identifier'         => 'My service',
            'cache_ttl'          => 300,
            'cache_serializer'   => function (Response $response) {
                return $response->getContent();
            },
            'cache_unserializer' => function ($data) {
                return new Response($data);
            }
        );
        $response = new Response('My Response');
        $service = new CacheableService(new EventDispatcher(), new RestClient(), $options);
        $this->assertEquals(300, $service->getTtl());
        $service->setResponse($response);
        $this->assertEquals('My Response', $service->getDataForCache());
        $service->setDataFromCache('My Response');
        $this->assertEquals($response, $service->getResponse());
    }

    public function testHashKey()
    {
        $service = new CacheableService(new EventDispatcher(), new RestClient(), array('identifier' => 'cacheable service'));

        $service->setRequest(Request::create('/'));
        $key = $service->getHashKey();

        $this->assertNotNull($key);
        $this->assertTrue((bool) preg_match('/^cacheable_service.*/', $key));

        $service->setRequest(Request::create('/', 'GET', array(), array(), array(), array(), 'request body'));
        $newKey = $service->getHashKey();
        $this->assertNotEquals($key, $newKey);

        $service->setRequest(Request::create('/other'));
        $newKey = $service->getHashKey();
        $this->assertNotEquals($key, $newKey);

        $service->setRequest(Request::create('/', 'GET', array(), array(), array(), array('HTTP_CONTENT_TYPE' => 'application/json'), 'request body'));
        $newKey = $service->getHashKey();
        $this->assertNotEquals($key, $newKey);
    }
}
