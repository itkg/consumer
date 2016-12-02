<?php

namespace Itkg\Consumer\Listener;

use Itkg\Consumer\Event\ServiceCacheEvents;
use Itkg\Consumer\Event\ServiceEvent;
use Itkg\Consumer\Event\ServiceEvents;
use Itkg\Consumer\Service\AbstractService;
use Itkg\Consumer\Service\Service;
use Itkg\Consumer\Service\ServiceCacheableInterface;
use Itkg\Core\Cache\CacheableData;
use Itkg\Core\Cache\Event\CacheEvent;
use Itkg\Core\CacheableInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CacheListener
 *
 * Event listener for service caching (handler for Cacheable Service (implements CacheableInterface)
 *
 * @package Itkg\Consumer\Listener
 */
class CacheListener implements EventSubscriberInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            ServiceEvents::REQUEST  => 'onServiceRequest',
            ServiceEvents::RESPONSE => 'onServiceResponse'
        );
    }

    /**
     * @param ServiceEvent $event
     */
    public function onServiceRequest(ServiceEvent $event)
    {
        $service = $event->getService();

        if (!$service instanceof ServiceCacheableInterface || null === $service->getCacheAdapter() || $service->isNoCache()) {
            return;
        }

        $service->setIsLoaded(false);
        $cache = $this->createCache($service);
        $cachedService = $service->getCacheAdapter()->get($cache);
        // Check cache existence
        if ($cachedService instanceof AbstractService) {
            // Set data from cache to entity object
            $service->setIsLoaded(true);
            $service->setResponse($cachedService->getResponse());
            $this->eventDispatcher->dispatch(ServiceCacheEvents::LOADED, new ServiceEvent($service));
        }
    }

    /**
     * @param ServiceEvent $event
     */
    public function onServiceResponse(ServiceEvent $event)
    {
        $service = $event->getService();

        if (!$service instanceof ServiceCacheableInterface || null === $service->getCacheAdapter()) {
            return;
        }

        if (!$service->isLoaded()) {
            $cache = $this->createCache($service);
            $service->getCacheAdapter()->set($cache);
            $this->eventDispatcher->dispatch(ServiceCacheEvents::SET, new CacheEvent($cache));
        }
    }

    /**
     * @param AbstractService $service
     *
     * @return CacheableInterface|null
     */
    protected function createCache(AbstractService $service)
    {
        if (!$service instanceof ServiceCacheableInterface) {
            return null;
        }

        return new CacheableData(
            $service->getHashKey(),
            $service->getTtl(),
            $service
        );

    }
}
