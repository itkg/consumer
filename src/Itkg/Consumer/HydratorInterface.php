<?php

namespace Itkg\Consumer;

/**
 * Interface HydratorInterface
 * @package Itkg\Consumer
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
interface HydratorInterface
{
    /**
     * Insert data into object
     *
     * @param $object Ojbect to hydrate
     * @param $data   Data to insert
     * @param array $options List of options
     *
     * @return mixed
     */
    public function hydrate(&$object, $data, $options = array());
}