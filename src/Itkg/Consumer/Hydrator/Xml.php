<?php

namespace Itkg\Consumer\Hydrator;


use Itkg\Consumer\HydratorInterface;

/**
 * Class Xml
 * @package Itkg\Consumer\Hydrator
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class Xml implements HydratorInterface
{

    /**
     * Hydrate object with data
     *
     * @param mixed $object An object
     * @param mixed $data xml data (string or simple_xml)
     * @param array $options
     * @return mixed|void
     */
    public function hydrate(&$object, $data, $options = array())
    {
        if (!is_object($data)) {
            $data = simplexml_load_string($data);
        }
        if (isset($options['array_tag'])) {
            $arrayTag = $options['array_tag'];
        } else {
            $arrayTag = array();
        }

        if (isset($options['root'])) {
            $root = $options['root'];
        } else {
            $root = '';
        }

        $object = $this->xmlToObject($data, $root, $options['mapping'], $arrayTag, $object);

    }

    /**
     * Transform XML into object
     *
     * @param $xml simple_xml_object
     * @param $tagName A root tag name
     * @param array $mapping List of mappings
     * @param array $arrayTag List of array tags
     * @param null $object Object to hydrate
     * @return array|object|string
     */
    public function xmlToObject($xml, $tagName, $mapping = array(), $arrayTag = array(), $object = null)
    {

        if (!is_object($object)) {
            if (isset($mapping[$tagName])) {
                $object = new $mapping[$tagName];
            } else {
                return $this->xmlAsArray($xml, $arrayTag);
            }
        }
        $aVars = get_object_vars($xml);


        if (count($aVars)) {
            foreach ($aVars as $varName => $varValue) {
                // cas @attributes
                if ($varName == '@attributes') {
                    foreach ($varValue as $varNameTmp => $varValueTmp) {
                        $this->callSetter($object, $varNameTmp, (string)$varValueTmp);
                    }
                    // cas objet vide
                } else {
                    if (is_object($varValue) && is_null((string)$varValue)) {
                        $this->callSetter($object, $varName, null);
                        // cas objet non vide
                    } else {
                        if (is_object($varValue) && !empty($varValue)) {
                            $aVarsTmp = get_object_vars($varValue);
                            // cas objet non vide avec attribut non null
                            if (count($aVarsTmp)) {
                                // cas on force la mise sous forme de tableau à 1 élement
                                if (in_array($varName, $arrayTag)) {
                                    $varArray = array();
                                    $varArray[0] = $this->xmlToObject($varValue, $varName, $mapping, $arrayTag);
                                    $this->callSetter($object, $varName, $varArray);

                                    // objet
                                } else {
                                    $this->callSetter(
                                        $object,
                                        $varName,
                                        $this->xmlToObject($varValue, $varName, $mapping, $arrayTag)
                                    );
                                }
                                // cas objet non vide avec attribut null
                            } else {
                                $this->callSetter($object, $varName, null);
                            }
                            // cas tableau
                        } else {
                            if (is_array($varValue)) {
                                if (count($varValue)) {
                                    $varArray = array();
                                    foreach ($varValue as $varValueTmp) {
                                        $varArray[] = $this->xmlToObject($varValueTmp, $varName, $mapping, $arrayTag);
                                    }
                                    $this->callSetter($object, $varName, $varArray);
                                }
                                // cas type simple
                            } else {
                                $this->callSetter($object, $varName, (string)$varValue);
                            }
                        }
                    }
                }
            }
        }
        return $object;
    }

    /**
     * Transform xml to array
     *
     * @param $xml simpleXML
     * @param $arrayTag list of array tags
     * @return array|string
     */
    public function xmlAsArray($xml, $arrayTag)
    {
        if (is_object($xml) && get_class($xml) == 'SimpleXMLElement') {
            $attributes = $xml->attributes();
            foreach ($attributes as $k => $v) {
                if ($v) {
                    $a[$k] = (string)$v;
                }
            }
            $xml = get_object_vars($xml);
        }
        $r = array();
        if (is_array($xml)) {
            if (count($xml) == 0) {
                return (string)$xml;
            } // for CDATA
            foreach ($xml as $key => $value) {
                if (in_array($key, $arrayTag) && count($value) == 1) {
                    $varArray = array();
                    $varArray[0] = $this->xmlAsArray($value, $arrayTag);
                    $r[$key] = $varArray;
                    // objet
                } else {
                    $r[$key] = $this->xmlAsArray($value, $arrayTag);
                }
            }
            if (isset($a) && count($a) > 0) { // Attributes
                foreach ($a as $k => $v) {
                    $r[$k] = $v;
                }
            }
            unset($r['@attributes']);
            return $r;
        }
        return (string)$xml;
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
        }
    }
}