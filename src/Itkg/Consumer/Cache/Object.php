<?php
namespace Itkg\Consumer\Cache;

use Itkg\Cache\Object as BaseObject;
use Itkg\Consumer\Service;

/**
 * Class Object
 * @package Itkg\Consumer\Cache
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class Object extends BaseObject
{
    /**
     * A service object
     *
     * @var Service
     */
    protected $service;

    /**
     * Store a direct call into value
     */
    public function init()
    {
        $this->value = $this->service->directCall($this->params);
    }

    /**
     * Getter servcice
     *
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Setter service
     * @param Service $service A service object
     */
    public function setService(Service $service)
    {
        $this->service = $service;
    }
}