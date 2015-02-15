<?php

ini_set('display_errors', 1);

include_once __DIR__.'/../vendor/autoload.php';

$registry = new \Itkg\Core\Cache\Adapter\Registry();
$eventDispatcher =  new \Symfony\Component\EventDispatcher\EventDispatcher();
$eventDispatcher->addSubscriber(new \Itkg\Consumer\Listener\CacheListener($registry, $eventDispatcher));
$eventDispatcher->addSubscriber(new \Itkg\Consumer\Listener\LoggerListener());

$service =  new \Itkg\Consumer\Service\SimpleService(
    $eventDispatcher,
    new Itkg\Consumer\Client\RestClient(array(
        'timeout' => 10
    )),
    array(
        'identifier' => 'my test'
    )
);

$service->sendRequest(\Symfony\Component\HttpFoundation\Request::create('XXXX'))->getResponse();
$service =  new \Itkg\Consumer\Service\CacheableService(
    $eventDispatcher,
    new Itkg\Consumer\Client\RestClient(array(
        'timeout' => 10
    )),
    array(
        'cache_ttl' => 20,
        'identifier' => 'my test'
    )
);

$service->sendRequest(\Symfony\Component\HttpFoundation\Request::create('XXXX'))->getResponse();

$service =  new \Itkg\Consumer\Service\LoggableService(
    $eventDispatcher,
    new Itkg\Consumer\Client\RestClient(array(
        'timeout' => 10
    )),
    new \Monolog\Logger('my_logger', array(new \Monolog\Handler\StreamHandler('/tmp/test'))),
    array(
        'identifier' => 'my test'
    )
);

$service->sendRequest(\Symfony\Component\HttpFoundation\Request::create('XXXX'))->getResponse();