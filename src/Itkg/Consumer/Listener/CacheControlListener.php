<?php

namespace Itkg\Consumer\Listener;

use Itkg\Consumer\Cache\ServiceCacheQueueReaderInterface;
use Itkg\Consumer\Cache\ServiceCacheQueueWriterInterface;
use Itkg\Consumer\Event\ServiceCacheEvents;
use Itkg\Consumer\Event\ServiceEvent;
use Itkg\Consumer\Service\AbstractService;
use Itkg\Consumer\Service\ServiceCacheableInterface;
use Itkg\Core\Cache\AdapterInterface;
use Itkg\Core\Cache\CacheableData;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CacheControlListener
 */
class CacheControlListener implements EventSubscriberInterface
{
    /**
     * @var ServiceCacheQueueWriterInterface
     */
    private $queueWriter;

    /**
     * @param ServiceCacheQueueWriterInterface $queueWriter
     */
    public function __construct(ServiceCacheQueueWriterInterface $queueWriter)
    {
        $this->queueWriter = $queueWriter;
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
            ServiceCacheEvents::LOADED => 'onCacheLoaded',
        );
    }

    /**
     * @param ServiceEvent $event
     */
    public function onCacheLoaded(ServiceEvent $event)
    {
        $service = $event->getService();

        if (!$this->isEligibile($service)) {
            return;
        }

        $serviceHashKey = $service->getHashKey();
        $this->queueWriter->addItem($serviceHashKey, $serviceHashKey);
    }

    /**
     * @param AbstractService $service
     *
     * @return bool
     */
    private function isEligibile(AbstractService $service)
    {
        return $service instanceof ServiceCacheableInterface
            && null !== $service->getCacheAdapter()
            && $service->canBeWarmed()
            && $service->isObsolete();
    }
}
