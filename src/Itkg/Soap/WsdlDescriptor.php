<?php

namespace Itkg\Soap;

/**
 * Classe WsdlDescriptor
 *
 * Parse un WSDL pour en extraire les définitions de méthodes et de structures
 * 
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class WsdlDescriptor 
{
    /**
     * Chemin du wsdl
     * 
     * @var string
     */
    protected $wsdl;
    
    /**
     * Liste de méthodes
     * 
     * @var array
     */
    protected $methods;
    
    /**
     * liste de structures
     * 
     * @var array
     */
    protected $types;
    
    /**
     * Liste de types primitifs
     * 
     * @static
     * @var array 
     */
    static $primitives = array('int' ,'boolean', 'string', 'dateTime');
    
    /**
     * Constructeur
     * 
     * @param string $wsdl
     */
    public function __construct($wsdl = '')
    {
        $this->wsdl = $wsdl;
        $oClient = new Client($this->wsdl);
        
        // Parsing des méthodes
        if(is_array($aFunctions = $oClient->__getFunctions())){
            foreach($aFunctions as $sFunction)
            {
                preg_match('/(.* )(.*\\()(.* )/', $sFunction, $matches);
                  
                $oMethod = new Method($matches[2], $matches[1], $matches[3]);       
                $this->methods[$oMethod->getName()] = $oMethod;
            }
        }

        // Parsing des structures
        if(is_array($aTypes = $oClient->__getTypes())) {
            
            foreach($aTypes as $sType) {
               
                preg_match('/(.* )(.* )\{([A-Za-z;\\n ]*)/', $sType, $matches);
                
                if(count($matches) > 1) {
                    if(!isset($this->types[$matches[2]])) {
                        if(trim($matches[1]) == 'struct') {
                            $matches[1] = ucfirst($matches[2]);
                        }
                        $oType = new Type($matches[1], $matches[2], !in_array(trim($matches[1]), self::$primitives));
                        $this->types[ucFirst($oType->getName())] = $oType;
                        if(count($matches) > 2) {
                            $aSubTypes = explode(';', $matches[3]);
                            if(is_array($aSubTypes)) {
                                foreach($aSubTypes as $sSubType) {
                                     
                                    preg_match('/(.* )(.*)/', $sSubType, $matches);
                                     
                                    if(count($matches) > 1) {
                                        // Ajout du sous-type à la pile
                                        $oType->addType($matches[1], $matches[2], !in_array(trim($matches[1]), self::$primitives));
                                    } 
                                }
                                $this->types[ucFirst($oType->getName())] = $oType;
                            }
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Getter methods
     * 
     * @return array|null
     */
    public function getMethods()
    {
        return $this->methods;
    }
    
    /**
     * Getter types
     * 
     * @return array|null
     */
    public function getTypes()
    {
        return $this->types;
    }
    
    /**
     * Getter wsdl
     * 
     * @return string
     */
    public function getWsdl()
    {
        return $this->wsdl;
    }
    
    /**
     * Retourne un tableau de mapping pour une méthode données
     * 
     * @param string $method Le nom de la méthode
     * @param string $namespace Le namespace des classes métiers
     * @return array
     */
    public function getMethodMapping($method, $namespace = '') 
    {
        if(isset($this->methods[$method])) {
            $oMethod = $this->methods[$method];
            if(isset($this->types[$oMethod->getReturnType()])) {
                $oType = $this->types[$oMethod->getReturnType()];
                return $this->getMappingForType($oType, $namespace);
            }
        }
        
        return array();
    }
    
    /**
     * Retourne le mapping associé à une structure de donnée
     * 
     * @param \Itkg\Soap\Type $oType La structure de donnée
     * @param string $namespace Le namespace des classes métiers à utiliser
     * @return string
     */
    public function getMappingForType($oType, $namespace = '') 
    {
        $aMapping = array();
  
        if(is_array($oType->getTypes())) {
            foreach($oType->getTypes() as $oSubType) {
                if($oSubType->isComplex() && isset($this->types[$oSubType->getName()])) {
                    $aMapping = array_merge($aMapping, $this->getMappingForType($this->types[$oSubType->getName()], $namespace));
                }
            }
            if($oType->isComplex()) {
                $aMapping[$oType->getVar()] = $namespace.'\\'.$oType->getName(); 
            }
        }else {
            if($oType->isComplex()) {
                $aMapping[$oType->getVar()] = $namespace.'\\'.$oType->getName(); 
            }
        }
        
        return $aMapping;
    }
}
