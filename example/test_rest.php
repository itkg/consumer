<?php

include_once __DIR__.'/../vendor/autoload.php';

$registry = new \Itkg\Core\Cache\Adapter\Registry();
$eventDispatcher =  new \Symfony\Component\EventDispatcher\EventDispatcher();
$eventDispatcher->addSubscriber(new \Itkg\Consumer\Listener\CacheListener($eventDispatcher));
$eventDispatcher->addSubscriber(new \Itkg\Consumer\Listener\LoggerListener());
$eventDispatcher->addSubscriber(new \Itkg\Consumer\Listener\DeserializerListener(JMS\Serializer\SerializerBuilder::create()->build()));

$service =  new \Itkg\Consumer\Service\Service(
    $eventDispatcher,
    new Itkg\Consumer\Client\RestClient(array(
        'timeout' => 10
    )),
    array(
        'identifier' => 'my test'
    )
);

$service->sendRequest(\Symfony\Component\HttpFoundation\Request::create('XXXX'))->getResponse();
$service =  new \Itkg\Consumer\Service\Service(
    $eventDispatcher,
    new Itkg\Consumer\Client\RestClient(array(
        'timeout' => 10
    )),
    array(
        'cache_ttl'     => 20,
        'cache_adapter' => $registry,
        'identifier'    => 'my test'
    )
);

$service->sendRequest(\Symfony\Component\HttpFoundation\Request::create('XXXX'))->getResponse();

$service =  new \Itkg\Consumer\Service\Service(
    $eventDispatcher,
    new Itkg\Consumer\Client\RestClient(array(
        'timeout' => 10
    )),
    array(
        'identifier' => 'my test',
        'logger'     => new \Monolog\Logger('my_logger', array(new \Monolog\Handler\StreamHandler('/tmp/test'))),
    )
);

$response = $service->sendRequest(\Symfony\Component\HttpFoundation\Request::create('XXXX'))->getResponse();
