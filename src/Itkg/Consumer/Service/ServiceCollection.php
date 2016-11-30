<?php

namespace Itkg\Consumer\Service;

use Iterator;
use Itkg\Consumer\Exception\ServiceNotFoundException;

/**
 * Class ServiceCollection
 */
class ServiceCollection implements Iterator
{
    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var array
     */
    private $services = array();

    /**
     * @param AbstractService $service
     *
     * @return $this
     */
    public function addService(AbstractService $service)
    {
        $this->services[] = $service;

        return $this;
    }

    /**
     * @param $identifier
     *
     * @return AbstractService
     * @throws ServiceNotFoundException
     */
    public function getServiceByIdentifier($identifier)
    {
        /** @var AbstractService $service */
        foreach ($this->services as $service) {
            if ($identifier === $service->getIdentifier()) {
                return $service;
            }
        }

        throw new ServiceNotFoundException($identifier);
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->services);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        $this->position ++;
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return isset($this->services[$this->position]);
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        $this->position = 0;
    }
}
