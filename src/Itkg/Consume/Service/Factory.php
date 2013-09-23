<?php

namespace Itkg\Consume\Service;

use Itkg\Exception\NotFoundException;

class Factory
{
    public function getService($id)
    {
        // Retrieve service
        if(!\Itkg::has($id)) {
            throw new NotFoundException(sprintf(
                'Service %s is not defined', $id
            ));
        }
        $service  = \Itkg::get($id);
        // Init service configuration

        return $service;
    }
}