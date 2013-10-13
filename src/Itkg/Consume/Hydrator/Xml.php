<?php

namespace Itkg\Consume\Hydrator;


use Itkg\Consume\HydratorInterface;

class Xml implements HydratorInterface
{

    public function hydrate(&$object, $datas, $options = array())
    {
        if(!is_object($datas)) {
            $datas = simplexml_load_string($datas);
        }
        $datas = $this->xmlToArray($datas, $options['mapping']);
        $simple = new Simple();
        $simple->hydrate($object, $datas, $options);
    }

    public function xmlToArray($xml, $arrayTag)
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
                    $varArray[0] = $this->xmlToArray($value, $arrayTag);
                    $r[$key] = $varArray;
                    // object
                } else {
                    $r[$key] = $this->xmlToArray($value, $arrayTag);
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
}