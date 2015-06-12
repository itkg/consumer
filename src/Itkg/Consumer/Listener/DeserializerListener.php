<?php

namespace Itkg\Consumer\Listener;

use Itkg\Consumer\Event\ServiceEvent;
use Itkg\Consumer\Event\ServiceEvents;
use Itkg\Consumer\Service\Service;
use Itkg\Consumer\Service\ServiceConfigurableInterface;
use JMS\Serializer\Serializer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DeserializerListener
 *
 * Event listener responsible of response deserialization
 * Use response_type & response_format options
 *
 * set deserializedContent property of service's response
 *
 * @package Itkg\Consumer\Listener
 */
class DeserializerListener implements EventSubscriberInterface
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
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
            ServiceEvents::RESPONSE => array('onResponseEvent', 0)
        );
    }

    /**
     * @param ServiceEvent $event
     */
    public function onResponseEvent(ServiceEvent $event)
    {

        $service = $event->getService();

        if ($service instanceof ServiceConfigurableInterface && null === $service->getOption('response_type')) {
            /** @var Service $service */
            $service->getResponse()->setDeserializedContent(
                $this->serializer->deserialize(
                    $service->getResponse()->getContent(),
                    $service->getOption('response_type'),
                    $service->getOption('response_format')
                )
            );
        }
    }

    /**
     * @param Serializer $serializer
     *
     * @return $this
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }
}
