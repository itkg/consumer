<?php

namespace Itkg\Consumer\Hydrator;


use Itkg\Consumer\HydratorInterface;

class Json implements HydratorInterface
{

    public function hydrate(&$object, $data, $options = array())
    {
        $data = json_decode($data, true);

        $simple = new Simple();
        $simple->hydrate($object, $data, $options);
    }
}