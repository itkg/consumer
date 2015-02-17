<?php

namespace Itkg\Consumer\Event;

use Itkg\Consumer\Service\Service;
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
     * @var Service
     */
    private $service;

    /**
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }
    /**
     * @return SimpleService
     */
    public function getService()
    {
        return $this->service;
    }
}
