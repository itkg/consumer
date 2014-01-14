<?php

namespace Itkg\Soap;

/**
 * Classe Type
 *
 * Représente une structure WSDL
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class Type
{
    /**
     * Liste de variables
     * @var array
     */
    protected $types;

    /**
     * Nom de la structure
     * @var string
     */
    protected $name;

    /**
     * Complexité de la structure
     * @var boolean
     */
    protected $complex;

    /**
     * libellé de la variable représentant la structure
     * @var string
     */
    protected $var;

    /**
     * Constructeur
     *
     * @param string $name
     * @param string $var
     * @param boolean $complex
     */
    public function __construct($name, $var, $complex)
    {
        $this->name = trim($name);
        $this->complex = trim($complex);
        $this->var = trim($var);
    }

    /**
     * Ajout d'un type à la pile
     *
     * @param string $name
     * @param string $var
     * @param boolean $complex
     */
    public function addType($name, $var, $complex)
    {
        $this->types[$var] = new Type($name, $var, $complex);
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
     * Setter name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Getter complex
     *
     * @return boolean
     */
    public function isComplex()
    {
        return $this->complex;
    }

    /**
     * Getter var
     *
     * @return string
     */
    public function getVar()
    {
        return $this->var;
    }

    /**
     * Setter var
     *
     * @param string $var
     */
    public function setVar($var)
    {
        $this->var = $var;
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
}
