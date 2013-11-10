<?php

namespace Itkg\Consumer\Event;


use Itkg\Consumer\Event\FilterServiceEvent;
use Itkg\Consumer\Service\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ServiceDebugSubscriber
 * @package Itkg\Consumer\Event
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class ServiceDebugSubscriber implements EventSubscriberInterface
{

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
            Events::BIND_REQUEST => array('onBindRequest', 0),
            Events::BIND_RESPONSE => array('onBindResponse', 0),
            Events::PRE_CALL => array('onPreCall', 0),
            Events::POST_CALL => array('onPostCall', 0),
            Events::FAIL_CALL => array('onFailCall', 0),
            Events::SUCCESS_CALL => array('onSuccessCall', 0),
            Events::FROM_CACHE => array('onCacheCall', 0),
            Events::PRE_AUTHENTICATE => array('onPreAuthenticate', 0),
            Events::FAIL_AUTHENTICATE => array('onFailAuthenticate', 0),
            Events::SUCCESS_AUTHENTICATE => array('onSuccessAuthenticate', 0)
        );
    }

    /**
     * Is called on bind request
     *
     * @param FilterServiceEvent $event An event
     */
    public function onBindRequest(FilterServiceEvent $event)
    {
        foreach ($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::PRE_CALL);
            $logger->debug(
                json_encode($event->getService()->getRequest()->getDatas())
            );
        }
    }

    /**
     * Is called on bind response
     *
     * @param FilterServiceEvent $event An event
     */
    public function onBindResponse(FilterServiceEvent $event)
    {
        foreach ($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::PRE_CALL);
            $logger->debug(
                $event->getService()->getResponse()->getHeader() . $event->getService()->getResponse()->getBody()
            );
        }
    }

    /**
     * Is called before service call
     *
     * @param FilterServiceEvent $event An event
     */
    public function onPreCall(FilterServiceEvent $event)
    {
        foreach ($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::PRE_CALL);
            $logger->debug($event->getService()->getRequest()->toLog());
        }
    }

    /**
     * Is called after success service call
     *
     * @param FilterServiceEvent $event An event
     */
    public function onSuccessCall(FilterServiceEvent $event)
    {
        foreach ($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::SUCCESS_CALL);
            $logger->debug($event->getService()->getResponse()->toLog());
        }
    }

    /**
     * Is called after fail service call
     *
     * @param FilterServiceEvent $event An event
     */
    public function onFailCall(FilterServiceEvent $event)
    {
        foreach ($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::FAIL_CALL);
            $logger->debug($event->getService()->getResponse()->toLog());
        }
    }

    /**
     * Is called after service call
     *
     * @param FilterServiceEvent $event An event
     */
    public function onPostCall(FilterServiceEvent $event)
    {
        foreach ($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::POST_CALL);
            $logger->debug($event->getService()->getResponse()->toLog());
        }
    }

    /**
     * Is called before authenticate
     *
     * @param FilterServiceEvent $event An event
     */
    public function onPreAuthenticate(FilterServiceEvent $event)
    {
        foreach ($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::PRE_AUTHENTICATE);
            $logger->debug($event->getService()->getResponse()->toLog());
        }
    }

    /**
     * Is called after fail authenticate
     *
     * @param FilterServiceEvent $event An event
     */
    public function onFailAuthenticate(FilterServiceEvent $event)
    {
        foreach ($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::FAIL_AUTHENTICATE);
            $logger->debug($event->getService()->getResponse()->toLog());
        }
    }

    /**
     * Is called after success authenticate
     *
     * @param FilterServiceEvent $event An event
     */
    public function onSuccessAuthenticate(FilterServiceEvent $event)
    {
        foreach ($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::SUCCESS_AUTHENTICATE);
            $logger->debug($event->getService()->getResponse()->toLog());
        }
    }

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