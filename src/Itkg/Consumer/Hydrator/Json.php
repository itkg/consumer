<?php

namespace Itkg\Consumer\Hydrator;


use Itkg\Consumer\HydratorInterface;

class Json implements HydratorInterface
{

    public function hydrate(&$object, $datas, $options = array())
    {
        $datas = json_decode($datas, true);

        $simple = new Simple();
        $simple->hydrate($object, $datas, $options);
    }
}