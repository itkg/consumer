<?php
namespace Itkg\Consumer\Cache;

use Itkg\Cache\Object as BaseObject;
use Itkg\Consumer\Service;

class Object extends BaseObject
{
    protected $service;

    public function init()
    {
        $this-> value = $this->service->directCall($this->params);
    }

    public function getService()
    {
        return $this->service;
    }

    public function setService(Service $service)
    {
        $this->service = $service;
    }
}