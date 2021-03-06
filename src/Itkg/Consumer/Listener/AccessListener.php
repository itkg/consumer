<?php

namespace Itkg\Consumer\Listener;

use Itkg\Consumer\Event\ServiceEvent;
use Itkg\Consumer\Event\ServiceEvents;
use Itkg\Consumer\Exception\DisabledServiceException;
use Itkg\Consumer\Service\AdvancedServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AccessListener
 *
 * @package Itkg\Consumer\Listener
 */
class AccessListener implements EventSubscriberInterface
{
    /**
     * throw exception if service is disabled
     *
     * @param ServiceEvent $event
     * @throws \Itkg\Consumer\Exception\DisabledServiceException
     */
    public function onServiceRequest(ServiceEvent $event)
    {
        $service = $event->getService();

        if ($service instanceof AdvancedServiceInterface && $service->isDisabled()) {
            throw new DisabledServiceException(
                sprintf(
                    'Service %s is disabled',
                    $service->getIdentifier()
                )
            );
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
            ServiceEvents::REQUEST => array('onServiceRequest', 10)
        );
    }
}
