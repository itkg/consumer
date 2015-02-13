<?php

ini_set('display_errors', 1);

include_once __DIR__.'/../vendor/autoload.php';

$registry = new \Itkg\Core\Cache\Adapter\Registry();
$eventDispatcher =  new \Symfony\Component\EventDispatcher\EventDispatcher();
$eventDispatcher->addSubscriber(new \Itkg\Consumer\Listener\CacheListener($registry, $eventDispatcher));
$eventDispatcher->addSubscriber(new \Itkg\Consumer\Listener\LoggerListener());

$service =  new \Itkg\Consumer\Service\LightService(
    $eventDispatcher,
    new Itkg\Consumer\Client\RestClient(array(
        'timeout' => 10
    )),
    \Symfony\Component\HttpFoundation\Request::create('XXXX'),
    new \Symfony\Component\HttpFoundation\Response(),
    array(
        'identifier' => 'my test'
    )
);

$service->sendRequest()->getResponse();
$service =  new \Itkg\Consumer\Service\CacheableService(
    $eventDispatcher,
    new Itkg\Consumer\Client\RestClient(array(
        'timeout' => 10
    )),
    \Symfony\Component\HttpFoundation\Request::create('XXXX'),
    new \Symfony\Component\HttpFoundation\Response(),
    array(
        'cache_ttl' => 20,
        'identifier' => 'my test'
    )
);

$service->sendRequest()->getResponse();

$service =  new \Itkg\Consumer\Service\LoggableService(
    $eventDispatcher,
    new Itkg\Consumer\Client\RestClient(array(
        'timeout' => 10
    )),
    new \Monolog\Logger('my_logger', array(new \Monolog\Handler\StreamHandler('/tmp/test'))),
    \Symfony\Component\HttpFoundation\Request::create('XXXX'),
    new \Symfony\Component\HttpFoundation\Response(),
    array(
        'identifier' => 'my test'
    )
);
$service->sendRequest()->getResponse();