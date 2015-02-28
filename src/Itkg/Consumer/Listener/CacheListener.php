<?php

namespace Itkg\Consumer\Listener;

use Itkg\Consumer\Event\ServiceEvent;
use Itkg\Consumer\Event\ServiceEvents;
use Itkg\Consumer\Service\ServiceCacheableInterface;
use Itkg\Core\Cache\Event\CacheEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
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
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param ServiceEvent $event
     */
    public function onServiceRequest(ServiceEvent $event)
    {
        $service = $event->getService();

        if (!$service instanceof ServiceCacheableInterface || null === $service->getCacheAdapter()) {
            return;
        }
        // Check cache existence
        if (false !== $data = $service->getCacheAdapter()->get($service)) {

            // Set data from cache to entity object
            $service->setDataFromCache($data);
            $service->setIsLoaded(true);
            $this->eventDispatcher->dispatch('cache.load', new CacheEvent($service));
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
            $service->getCacheAdapter()->set($service);
            $this->eventDispatcher->dispatch('cache.set', new CacheEvent($service));
        }
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
}
