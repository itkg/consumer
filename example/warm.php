<?php

include_once __DIR__.'/../vendor/autoload.php';
ini_set('display_errors', 1);
$adapter = new Itkg\Core\Cache\Adapter\Bridge\Doctrine(new \Doctrine\Common\Cache\FilesystemCache('/tmp'));
$registry = new \Itkg\Core\Cache\Adapter\Registry();
$eventDispatcher =  new \Symfony\Component\EventDispatcher\EventDispatcher();
$eventDispatcher->addSubscriber(new \Itkg\Consumer\Listener\CacheListener($eventDispatcher));
$eventDispatcher->addSubscriber(new \Itkg\Consumer\Listener\LoggerListener());
$eventDispatcher->addSubscriber(new \Itkg\Consumer\Listener\DeserializerListener(JMS\Serializer\SerializerBuilder::create()->build()));
$eventDispatcher->addSubscriber(new \Itkg\Consumer\Listener\CacheControlListener(new \Itkg\Consumer\Cache\ServiceCacheQueueWriter($adapter)));

$service =  new \Itkg\Consumer\Service\Service(
    $eventDispatcher,
    new Itkg\Consumer\Client\RestClient(array(
        'timeout' => 10
    )),
    array(
        'identifier' => 'my test',
        'cache_adapter' => $adapter,
        'cache_ttl'     => 3600,
        'cache_warmup'  => true,
        'cache_fresh_ttl' => 30
    )
);

$services = new \Itkg\Consumer\Service\ServiceCollection();
$services->addService($service);

$processor = new \Itkg\Consumer\Cache\ServiceCacheQueueProcessor(
    new \Itkg\Consumer\Cache\ServiceCacheQueueReader($adapter),
    new \Itkg\Consumer\Cache\ServiceCacheQueueWriter($adapter),
    new \Itkg\Consumer\Cache\ServiceCacheWarmer(),
    $adapter,
    $services
);

$processor->processAll();
