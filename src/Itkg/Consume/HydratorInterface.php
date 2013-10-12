<?php

namespace Itkg\Consume;


interface HydratorInterface
{
    public function hydrate(&$object, $datas, $options = array());
}