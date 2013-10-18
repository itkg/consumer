<?php

namespace Itkg\Consume\Service\Event;

use Itkg\Consume\Service;
use Symfony\Component\EventDispatcher\Event;

class FilterServiceEvent extends Event
{
    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function getService()
    {
        return $this->service;
    }
}