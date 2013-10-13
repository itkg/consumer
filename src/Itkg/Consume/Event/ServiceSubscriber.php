<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ubuntu
 * Date: 10/13/13
 * Time: 4:32 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Itkg\Consume\Event;


use Itkg\Consume\Service\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Itkg\Consume\Event\FilterServiceEvent;

class ServiceSubscriber implements EventSubscriberInterface
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
    {}

    public function onBindResponse(FilterServiceEvent $event)
    {}

    public function onPreCall(FilterServiceEvent $event)
    {

    }

    public function onPostCall(FilterServiceEvent $event)
    {

    }

    public function onSuccessCall(FilterServiceEvent $event)
    {

    }

    public function onFailCall(FilterServiceEvent $event)
    {

    }
}