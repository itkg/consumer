<?php

namespace Itkg\Service;

/**
 * Class abstraite représentant un model simple
 * Ce modèle représente une requete ou une réponse d'appel à un WS
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @abstract
 * @package \Itkg\Service
 */
abstract class Model
{
    /**
     *
     * @var Itkg\Validator
     */
    protected $validator;

    /**
     * Liste d'erreurs générées par le validator
     *
     * @var array
     */
    protected $errors;

    /**
     * Les données brutes injectées dans le modèle via la méthode injectDatas
     * @var array
     */
    protected $datas;
    
    /**
     * Retourne l'objet validator
     *
     * @return Itkg\Service\Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Set un validator
     *
     * @param Itkg\Service\Validator $validator
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Méthode de validation de l'objet courant
     *
     * @return boolean
     */
    public function validate() {
        if(is_object($this->validator)) {
            $this->errors = $this->validator->validate($this);
        }

        return (sizeof($this->errors) == 0);
    }

    /**
     * Permet l'initialisation de certains attributs du model ou de certains traitements
     */
    public function init() {}

    /**
     * Utilisé pour les classMap SOAP
     *
     * @return $string le nom du model courant
     */
    public function getClassMap()
    {
        return get_class($this);
    }

    /**
     * Liste des erreurs générées après validation
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Converti les données d'un tableau en attribut du modèle
     * Doit être redéfini pour les types complexes
     *
     * @param array $aDatas
     */
    public function injectDatas($aDatas = array())
    {
        if(!is_array($aDatas)) {
            $aDatas = array();
        }
        $this->datas = $aDatas;

        \Itkg\Helper\DataTransformer::arrayIntoObject($this, $aDatas, true);
    }
    /**
     * Renvoi l'objet sous forme XML (pour modéliser une request soap par exemple)
     *
     * @return string
     */
    public function __toXML() {}

    /**
     * Renvoi l'objet sous forme de chaines de caractères (utilisable pour logguer l'objet)
     *
     * @return mixed
     */
    public function __toLog() {}

    /**
     * Formate l'objet courant sous forme de request (Utilisé pour SOAP ou REST)
     * Par défaut renvoie l'appel de la méthode __toXML
     *
     * @return mixed
     */
    public function __toRequest()
    {
        return $this->__toXML();
    }

    /**
     * Retourne l'objet sous forme de tableau
     * en supprimant les éléments inutiles
     *  
     * @return array
     */
    public function __toArray()
    {
        $values = get_object_vars($this);
        unset($values['datas']);
        unset($values['errors']);
        unset($values['validator']);
        
        return $values;

    }

    /**
     * Renvoie les données brutes injectées dans le modèle
     *
     * @return array
     */
    public function __toDatas()
    {
        return $this->datas;
    }

    /**
     * Setter magique
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);
        if (method_exists($this, $method)) {
            $this->$method($value);
        }else {
            $this->$name = $value;
        }
    }

    /**
     * Getter magique
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name;
    }
    
    /**
     * méthode magique appelée automagiquement 
     * lors d'un appel à empty() sur un attribut protégé
     * Cela afin de pouvoir tester si un attribut est empty sans le stocker
     * dans une variable
     * 
     * @param string $name
     * @return boolean
     */
    public function __isset($name){
        return isset($this->$name);
    } 
}
