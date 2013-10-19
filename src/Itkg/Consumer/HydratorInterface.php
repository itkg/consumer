<?php

namespace Itkg\Consumer;


interface HydratorInterface
{
    public function hydrate(&$object, $datas, $options = array());
}