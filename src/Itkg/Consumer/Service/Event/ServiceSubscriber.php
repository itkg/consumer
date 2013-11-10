<?php


namespace Itkg\Consumer\Service\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Class ServiceSubscriber
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
abstract class ServiceSubscriber implements EventSubscriberInterface
{
    /**
     * Get loggers from an event
     *
     * @param FilterServiceEvent $event An event
     * @return array
     */
    protected function getLoggers(FilterServiceEvent $event)
    {
        return $event->getService()->getLoggers();
    }
}