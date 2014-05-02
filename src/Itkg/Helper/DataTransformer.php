<?php

namespace Itkg\Helper;

/**
 * Classe utilitaires pour la transformation de données
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @abstract
 * @package \Itkg\Helper
 */
class DataTransformer
{

    /**
     * converti un objet simpleXML en tableau
     *
     * @param object $xml
     * @return array
     * @param array $arrayTag
     */
    public static function simplexml2array($xml, $arrayTag = array())
    {
        if (is_object($xml) && get_class($xml) == 'SimpleXMLElement') {
            $attributes = $xml->attributes();
            foreach ($attributes as $k => $v) {
                if ($v) {
                    $a[$k] = (string)$v;
                }
            }
            $x = $xml;
            $xml = get_object_vars($xml);
        }
        if (is_array($xml)) {
            if (count($xml) == 0) {
                return (string)$x;
            } // for CDATA
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
            if (isset($a) && count($a) > 0) { // Attributes
                //$r['@attributes'] = $a;
                foreach ($a as $k => $v) {
                    $r[$k] = $v;
                }
            }
            return $r;
        }
        return (string)$xml;
    }

    /**
     * converti un string xml en objet avec imbrication d'objets
     * En fonction d'un tableau de mapping il est possible de spécifier pour
     * chaque noeud l'objet correspondant
     *
     * @param string xml
     * @param string $tagName
     * @param array $mapping
     * @param array $arrayTag
     * @return object
     */
    public static function stringxmlToObject($stringXml, $tagName, $mapping = array(), $arrayTag = array())
    {

        $xml = simplexml_load_string($stringXml);
        $object = self::simplexmlToObject($xml, $tagName, $mapping, $arrayTag);

        return $object;
    }

    /**
     * converti un objet simpleXML en objet avec imbrication d'objets
     * En fonction d'un tableau de mapping il est possible de spécifier pour
     * chaque noeud l'objet correspondant
     * Dans le cas d'un objet dans le xml sans correspondance dans le mapping, i
     * il n'y a pas de création d'objet simpleXml par défaut
     * En fonction d'un tableau $arrayTag il est possible de spécifier pour
     * chaque noeud si on veut forcer une mise sous forme de tableau
     *
     * @param object|string $xml
     * @param string $tagName
     * @param array $mapping
     * @param array $arrayTag
     * @return object|string
     */
    public static function simplexmlToObject($xml, $tagName, $mapping = array(), $arrayTag = array())
    {
        // création de la classe en fct du mapping sinon retour de la valeur
        if (isset($mapping[$tagName])) {
            try {
                $object = new $mapping[$tagName];
            } catch (Exception $exc) {

                //return $xml; //retourne des objets simpleXml
                return null; // ne retourne pas d'objet simpleXml
            }
        } else {

            //return $xml; //retourne des objets simpleXML
            return null; // ne retourne pas d'objet simpleXml
        }
        if (is_object($xml)) {
            $aVars = get_object_vars($xml);
        }
        if (count($aVars)) {
            foreach ($aVars as $varName => $varValue) {
                // cas @attributes
                if ($varName == '@attributes') {
                    foreach ($varValue as $varNameTmp => $varValueTmp) {
                        $object->$varNameTmp = (string)$varValueTmp;
                    }
                    // cas objet vide
                } else {
                    if (is_object($varValue) && is_null((string)$varValue)) {
                        $object->$varName = null;
                        // cas objet non vide
                    } else {
                        if (is_object($varValue) && !empty($varValue)) {
                            $aVarsTmp = get_object_vars($varValue);
                            // cas objet non vide avec attribut non null
                            if (count($aVarsTmp)) {
                                // cas on force la mise sous forme de tableau à 1 élement
                                if (in_array($varName, $arrayTag)) {
                                    $varArray = array();
                                    $varArray[0] = self::simplexmlToObject($varValue, $varName, $mapping, $arrayTag);
                                    $object->$varName = $varArray;
                                    // objet
                                } else {

                                    $object->$varName = self::simplexmlToObject(
                                        $varValue,
                                        $varName,
                                        $mapping,
                                        $arrayTag
                                    );
                                }
                                // cas objet non vide avec attribut null
                            } else {
                                $object->$varName = null;
                            }
                            // cas tableau
                        } else {
                            if (is_array($varValue)) {
                                if (count($varValue)) {
                                    $varArray = array();
                                    foreach ($varValue as $varNameTmp => $varValueTmp) {
                                        $varArray[] = self::simplexmlToObject(
                                            $varValueTmp,
                                            $varName,
                                            $mapping,
                                            $arrayTag
                                        );
                                    }
                                    $object->$varName = $varArray;
                                }
                                // cas type simple
                            } else {
                                $object->$varName = (string)$varValue;
                            }
                        }
                    }
                }
            }
        }
        return $object;
    }

    /**
     * transforme un tableau en objet
     *
     * @param array $array
     * @return object
     */
    public static function arrayToObject($array)
    {
        $object = new \stdClass();

        foreach ($array as $key => $value) {

            if (is_array($value)) {
                $value = self::arrayToObject($value);
            }

            $object->{$key} = $value;
        }

        return $object;
    }

    /**
     * initialise des propriétés d'un objet à partir d'un tableau
     *
     * @param object $object
     * @param array $array
     * @return object
     */
    public static function arrayIntoObject($object, array $array = array(), $bUseMagicMethods = false)
    {
        if (is_array($array)) {
            if (!$bUseMagicMethods) {
                foreach ($array as $key => $value) {
                    $method = 'set' . ucfirst($key);
                    if (method_exists($object, $method)) {
                        $object->$method($value);
                    }
                }
            } else {
                //cas où l'on passe par les méthodes magiques
                foreach ($array as $key => $value) {
                    $property = $key;
                    if (property_exists($object, $property)) {
                        $object->$property = $value;
                    }
                }
            }
        }
        return $object;
    }

    /**
     * trim les données d'un objet ou d'un tableau
     *
     * @param object|array|string $data
     * @return object|array|string
     */
    public static function trimData($data)
    {
        if (is_object($data)) {
            return self::trimObject($data);
        }

        if (is_array($data)) {
            foreach ($data as $varName => $varValue) {
                $data[$varName] = self::trimData($varValue);
            }
        } else {
            $data = trim((string)$data);
        }

        return $data;
    }

    /**
     * Trim object attributes
     *
     * @param $data
     * @return mixed
     */
    protected static function trimObject($data)
    {
        $aVars = get_object_vars($data);
        if (is_array($aVars)) {
            foreach ($aVars as $varName => $varValue) {
                $data->$varName = self::trimData($varValue);
            }
        }

        return $data;
    }
}

