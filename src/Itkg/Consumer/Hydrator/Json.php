<?php

namespace Itkg\Consumer\Hydrator;


use Itkg\Consumer\HydratorInterface;

/**
 * Class Json
 * @package Itkg\Consumer\Hydrator
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class Json implements HydratorInterface
{

    /**
     * Hydrate json data into an object
     *
     * @param \Itkg\Consumer\Ojbect $object Object model
     * @param \Itkg\Consumer\Data $data List of params
     * @param array $options List of options
     * @return mixed|void
     */
    public function hydrate(&$object, $data, $options = array())
    {
        $data = json_decode($data, true);

        $simple = new Simple();
        $simple->hydrate($object, $data, $options);
    }
}