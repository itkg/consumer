<?php

namespace Itkg\Consumer\Event;

use Itkg\Consumer\Service\SimpleService;
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
     * @var SimpleService
     */
    private $service;

    /**
     * @param SimpleService $service
     */
    public function __construct(SimpleService $service)
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
