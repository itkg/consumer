<?php

namespace Itkg\Consumer\Hydrator;


use Itkg\Consumer\HydratorInterface;

/**
 * Class Simple
 * @package Itkg\Consumer\Hydrator
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class Simple implements HydratorInterface
{

    /**
     * Hydrate object with array of data
     *
     * @param \Itkg\Consumer\Ojbect $object An object
     * @param \Itkg\Consumer\Data $data List of params to inject
     * @param array $options List of options
     * @return mixed|void
     */
    public function hydrate(&$object, $data, $options = array())
    {

        $data = $this->subHydrate($object, $data, $options);

        if (isset($options['root_attribute'])) {
            $this->callSetter($object, $options['root_attribute'], $data);
        }
    }

    /**
     * Recursive method used to hydrate subObject graph
     *
     * @param $object An object
     * @param $data List of data
     * @param $options List of options
     * @return array
     */
    public function subHydrate(&$object, $data, $options)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (isset($options['mapping'][$key])) {

                    // Recursive injection
                    $subObject = new $options['mapping'][$key]();
                    $this->subHydrate($subObject, $value, $options);

                    $value = $subObject;
                } else {
                    if (is_array($value)) {
                        $value = $this->subHydrate($object, $value, $options);
                    }
                }

                // Check setMethod
                if (!$this->callSetter(
                    $object,
                    $this->toCamelCase($key),
                    $value
                )
                ) { // If no setter is defined, we reinject into array
                    $data[$key] = $value; // Change data with subObject
                }
            }
        }

        return $data;
    }

    /**
     * Transform "_" var to camelCase
     *
     * @param string $key Key to transform
     *
     * @return string The new key
     */
    public function toCamelCase($key)
    {
        $regexp = '#_(.)#e';
        return preg_replace($regexp, "strtoupper('\\1')", $key);
    }

    /**
     * Call object set method for a key and pass value
     *
     * @param $object An object
     * @param $key A key (setKey)
     * @param $value A value (setKey(value))
     * @return bool IF set method exist
     */
    public function callSetter(&$object, $key, $value)
    {
        $method = 'set' . ucfirst($key);
        if (method_exists($object, $method)) {
            $object->$method($value);

            return true;
        }

        return false;
    }
}