<?php

namespace Itkg\Consumer\Hydrator;


use Itkg\Consumer\HydratorInterface;

class Simple implements HydratorInterface
{

    public function hydrate(&$object, $datas, $options = array())
    {
        if(is_array($datas)) {
            foreach($datas as $key => $value) {
                if(isset($options['mapping'][$key])) {
                    // Recursive injection
                    $subObject = new $options['mapping'][$key]();
                    $this->hydrate($subObject, $value, $options);
                    $value = $subObject;
                }
                // Check setMethod
                $this->callSetter($object, $key, $value);
            }
        }
    }

    public function callSetter(&$object, $key, $value)
    {
        $method = 'set'.ucfirst($key);
        if(method_exists($object, $method)) {
            $object->$method($value);
        }
    }
}