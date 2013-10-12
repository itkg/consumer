<?php

namespace Itkg\Consume\Hydrator;


use Itkg\Consume\HydratorInterface;

class Json implements HydratorInterface
{

    public function hydrate(&$object, $datas, $options = array())
    {
        $datas = json_decode($datas, true);

        $simple = new Simple();
        $simple->hydrate($object, $datas, $options);
    }
}