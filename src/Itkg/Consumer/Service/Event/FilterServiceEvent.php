<?php

namespace Itkg\Consumer\Service\Event;

use Itkg\Consumer\Service;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class FilterServiceEvent
 * @package Itkg\Consumer\Service\Event
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class FilterServiceEvent extends Event
{
    /**
     * A service object
     *
     * @var \Itkg\Consumer\Service
     */
    protected $service;

    /**
     * Constructor
     *
     * @param Service $service Service object
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Getter service
     *
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }
}