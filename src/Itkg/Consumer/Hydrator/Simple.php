<?php

namespace Itkg\Consumer\Hydrator;


use Itkg\Consumer\HydratorInterface;

class Simple implements HydratorInterface
{

    public function hydrate(&$object, $data, $options = array())
    {

        $data = $this->subHydrate($object, $data, $options);

        if(isset($options['root_attribute'])) {
            $this->callSetter($object, $options['root_attribute'], $data);
        }
    }

    public function subHydrate(&$object, $data, $options)
    {
        if(is_array($data)) {
            foreach($data as $key => $value) {
                if(isset($options['mapping'][$key])) {

                    // Recursive injection
                    $subObject = new $options['mapping'][$key]();
                    $this->subHydrate($subObject, $value, $options);

                    $value = $subObject;
                }else if(is_array($value)) {
                    $value = $this->subHydrate($object, $value, $options);
                }

                // Check setMethod
                if(!$this->callSetter($object, $this->toCamelCase($key), $value)) { // If no setter is defined, we reinject into array
                    $data[$key] = $value; // Change data with subObject
                }
            }
        }

        return $data;
    }

    public function toCamelCase($key)
    {
        if(strrpos($key, '_')) {
            $keys = explode('_', $key);
            $key = '';
            foreach($keys as $k) {
                if($key) {
                    $key .= ucFirst($k);
                }else {
                    $key = $k;
                }
            }
        }
        print_r($key);
        return $key;
    }

    public function callSetter(&$object, $key, $value)
    {
        $method = 'set'.ucfirst($key);
        if(method_exists($object, $method)) {
            $object->$method($value);

            return true;
        }

        return false;
    }
}