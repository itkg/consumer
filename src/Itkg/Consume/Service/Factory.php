<?php

namespace Itkg\Consume\Service;


use Itkg\Exception\NotFoundException;

class Factory
{
    public function get($id)
    {
        // Retrieve WS method by its id
        if(!\Itkg::has($id)) {
            throw new NotFoundException(
                sprintf("Method %s does not exist", $id)
            );
        }
        return \Itkg::get($id);
    }
}