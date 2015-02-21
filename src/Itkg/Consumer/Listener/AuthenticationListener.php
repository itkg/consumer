<?php

namespace Itkg\Consumer\Listener;

use Itkg\Consumer\Event\ServiceEvent;
use Itkg\Consumer\Event\ServiceEvents;
use Itkg\Consumer\Service\ServiceAuthenticableInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class AuthenticationListener implements EventSubscriberInterface
{

    /**
     * Authenticate a service if needed & inject authenticated token
     *
     * @param ServiceEvent $event
     */
    public function onServiceRequest(ServiceEvent $event)
    {
        $service = $event->getService();

        if (!$service instanceof ServiceAuthenticableInterface) {
            return;
        }

        if (!$service->isAuthenticated()) {
            $service->authenticate();
        }

        // Inject authenticated data into the current request
        $service->makeAuthenticated();
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
        );
    }
}