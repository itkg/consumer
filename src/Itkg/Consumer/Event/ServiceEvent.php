<?php

namespace Itkg\Consumer\Event;

use Itkg\Consumer\Service\LightService;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ServiceEvent
 *
 * Service Event used for ServiceEvents dispatch
 *
 * @package Itkg\Consumer\Event
 */
class ServiceEvent extends Event
{
    /**
     * @var LightService
     */
    private $service;

    /**
     * @param LightService $service
     */
    public function __construct(LightService $service)
    {
        $this->service = $service;
    }
    /**
     * @return LightService
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param LightService $service
     *
     * @return $this
     */
    public function setService(LightService $service)
    {
        $this->service = $service;

        return $this;
    }
}
