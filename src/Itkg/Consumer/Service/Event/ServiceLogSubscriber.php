<?php

namespace Itkg\Consumer\Service\Event;


use Itkg\Consumer\Service\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Itkg\Consumer\Service\Event\FilterServiceEvent;

class ServiceLogSubscriber implements EventSubscriberInterface
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
            Events::PRE_CALL      => array('onPreCall', 0),
            Events::FAIL_CALL     => array('onFailCall', 0),
            Events::SUCCESS_CALL  => array('onSuccessCall', 0),
            Events::FROM_CACHE    => array('onCacheCall', 0),
            Events::PRE_AUTHENTICATE  => array('onPreAuthenticate', 0),
            Events::FAIL_AUTHENTICATE => array('onFailAuthenticate', 0),
            Events::SUCCESS_AUTHENTICATE => array('onSuccessAuthenticate', 0)
        );
    }

    public function onPreCall(FilterServiceEvent $event)
    {
        foreach($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::PRE_CALL);
            $logger->info($event->getService()->getRequest()->toLog());
        }
    }
    public function onSuccessCall(FilterServiceEvent $event)
    {
        foreach($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::SUCCESS_CALL);
            $logger->info($event->getService()->getResponse()->toLog());
        }
    }

    public function onFailCall(FilterServiceEvent $event)
    {
        foreach($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::FAIL_CALL);
            $logger->error($event->getService()->getResponse()->toLog());
        }
    }

    public function onCacheCall(FilterServiceEvent $event)
    {
        foreach($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::FROM_CACHE);
            $logger->info($event->getService()->getResponse()->toLog());
        }
    }

    public function onPreAuthenticate(FilterServiceEvent $event)
    {
        foreach($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::PRE_AUTHENTICATE);
            $logger->info($event->getService()->getResponse()->toLog());
        }
    }

    public function onFailAuthenticate(FilterServiceEvent $event)
    {
        foreach($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::POST_AUTHENTICATE);
            $logger->error($event->getService()->getResponse()->toLog());
        }
    }

    public function onSuccessAuthenticate(FilterServiceEvent $event)
    {
        foreach($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::POST_AUTHENTICATE);
            $logger->info($event->getService()->getResponse()->toLog());
        }
    }

    protected function getLoggers($event)
    {
        return $event->getService()->getLoggers();
    }
}