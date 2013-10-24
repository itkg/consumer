<?php

namespace Itkg\Consumer\Hydrator;


use Itkg\Consumer\HydratorInterface;

class Xml implements HydratorInterface
{

    public function hydrate(&$object, $data, $options = array())
    {
        if(!is_object($data)) {
            $data = simplexml_load_string($data);
        }
        if(isset($options['array_tag'])) {
            $arrayTag = $options['array_tag'];
        }else {
            $arrayTag = array();
        }

        if(isset($options['root'])) {
            $root = $options['root'];
        }else {
            $root = '';
        }
        $object = $this->xmlToObject($data, $root, $options['mapping'], $arrayTag, $object);

    }

    public function xmlToObject($xml, $tagName, $mapping = array(), $arrayTag = array(), $object = null)
    {
        if(is_object($object)) {

        }else {
            if (isset($mapping[$tagName])) {
                try {
                    $object = new $mapping[$tagName];
                } catch (Exception $exc) {
                    //return $xml; //retourne des objets simpleXml
                    return null; // ne retourne pas d'objet simpleXml
                }
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
                        $this->callSetter($object, $varNameTmp, (string) $varValueTmp);
                    }
                    // cas objet vide
                } else if (is_object($varValue) && is_null((string) $varValue)) {
                    $this->callSetter($object, $varName, null);
                    // cas objet non vide
                } else if (is_object($varValue) && !empty($varValue)) {
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
                            $this->callSetter($object, $varName, $this->xmlToObject($varValue, $varName, $mapping, $arrayTag));
                        }
                        // cas objet non vide avec attribut null
                    } else {
                        $this->callSetter($object, $varName, null);
                    }
                    // cas tableau
                } else if (is_array($varValue)) {
                    if (count($varValue)) {
                        $varArray = array();
                        foreach ($varValue as $varNameTmp => $varValueTmp) {
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
        return $object;
    }

    public function xmlAsArray($xml, $arrayTag)
    {
        if (is_object($xml) && get_class($xml) == 'SimpleXMLElement') {
            $attributes = $xml->attributes();
            foreach ($attributes as $k => $v) {
                if ($v)
                    $a[$k] = (string) $v;
            }
            $x = $xml;
            $xml = get_object_vars($xml);
        }
        if (is_array($xml)) {
            if (count($xml) == 0)
                return (string) $x; // for CDATA
            foreach ($xml as $key => $value) {
                if (in_array($key, $arrayTag) && count($value) == 1) {
                    $varArray = array();
                    $varArray[0] = self::simplexml2array($value, $arrayTag);
                    $r[$key] = $varArray;
                    // objet
                } else {
                    $r[$key] = self::simplexml2array($value, $arrayTag);
                }
            }
            if (isset($a) && count($a) > 0) {    // Attributes
                //$r['@attributes'] = $a;
                foreach ($a as $k => $v) {
                    $r[$k] = $v;
                }
            }
            return $r;
        }
        return (string) $xml;
    }


    public function callSetter(&$object, $key, $value)
    {
        $method = 'set'.ucfirst($key);
        if(method_exists($object, $method)) {
            $object->$method($value);
        }
    }
}