<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ubuntu
 * Date: 10/13/13
 * Time: 4:32 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Itkg\Consumer\Event;


use Itkg\Consumer\Service\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Itkg\Consumer\Event\FilterServiceEvent;

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
            Events::BIND_REQUEST  => array('onBindRequest', 0),
            Events::BIND_RESPONSE => array('onBindResponse', 0),
            Events::PRE_CALL      => array('onPreCall', 0),
            Events::POST_CALL     => array('onPostCall', 0),
            Events::FAIL_CALL     => array('onFailCall', 0),
            Events::SUCCESS_CALL  => array('onSuccessCall', 0)
        );
    }

    public function onBindRequest(FilterServiceEvent $event)
    {
        foreach($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::PRE_CALL);
            $logger->debug(
                json_encode($event->getService()->getRequest()->getDatas())
            );
        }
    }

    public function onBindResponse(FilterServiceEvent $event)
    {
        foreach($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::PRE_CALL);
            $logger->debug(
                $event->getService()->getResponse()->getHeader().$event->getService()->getResponse()->getBody()
            );
        }
    }

    public function onPreCall(FilterServiceEvent $event)
    {
        foreach($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::PRE_CALL);
            $logger->debug($event->getService()->getRequest()->toLog());
        }
    }
    public function onSuccessCall(FilterServiceEvent $event)
    {
        foreach($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::SUCCESS_CALL);
            $logger->debug($event->getService()->getResponse()->toLog());
        }
    }

    public function onFailCall(FilterServiceEvent $event)
    {
        foreach($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::FAIL_CALL);
            $logger->debug($event->getService()->getResponse()->toLog());
        }
    }

    public function onPostCall(FilterServiceEvent $event)
    {
        foreach($this->getLoggers($event) as $logger) {
            $logger->getFormatter()->addParam('EVENT', Events::POST_CALL);
            $logger->debug($event->getService()->getResponse()->toLog());
        }
    }

    protected function getLoggers($event)
    {
        return $event->getService()->getLoggers();
    }
}