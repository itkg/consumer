<?php

namespace Itkg\Soap;

/**
 * Classe Method
 *
 * Modélise une méthode de WSDL
 * 
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class Method 
{
    /**
     * Nom de la méthode
     * @var string
     */
    protected $name;
    
    /**
     * Type de retour
     * @var string
     */
    protected $returnType;
    
    /**
     * Type du parametre
     * 
     * @var string 
     */
    protected $parameterType;
    
    /**
     * Constructeur
     * 
     * @param string $name
     * @param string $returnType
     * @param string $parameterType
     */
    public function __construct($name, $returnType, $parameterType)
    {
        $this->name = str_replace(array(')', '('), array('', ''), $name);
        $this->returnType = trim(ucFirst($returnType));
     
        $this->parameterType = trim(ucFirst($parameterType));
    }
    
    /**
     * Setter name
     * 
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Getter name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Getter returnType
     * 
     * @return string
     */
    public function getReturnType()
    {
        return $this->returnType;
    }
    
    /**
     * Setter returnType
     * 
     * @param string $returnType
     */
    public function setReturnType($returnType)
    {
        $this->returnType = $returnType;
    }
    
    /**
     * Getter parameterType
     * 
     * @return string
     */
    public function getParameterType()
    {
        return $this->parameterType;
    }
    
    /**
     * Setter parameterType
     * 
     * @param string $parameterType
     */
    public function setParameterType($parameterType)
    {
        $this->parameterType = $parameterType;
    }
}