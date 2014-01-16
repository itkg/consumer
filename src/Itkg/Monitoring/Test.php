<?php

namespace Itkg\Monitoring;

/**
 * Classe Test
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
abstract class Test
{
    /**
     * Identifiant du test
     *
     * @var string
     */
    protected $identifier;

    /**
     * Le monitoring associé au test
     *
     * @var \Itkg\Monitoring
     */
    protected $monitoring;

    /**
     * Constructeur
     * Ajoute le test courant au monitoring via (self::$tests)
     *
     * @param string $identifier
     */
    public function __construct($identifier = '')
    {
        $this->identifier = $identifier;
        $this->monitoring = new \Itkg\Monitoring();
        $this->monitoring->addTest($this);
    }

    /**
     * Getter identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Setter identifier
     *
     * @param type $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Getter monitoring
     *
     * @return \Itkg\Monitoring
     */
    public function getMonitoring()
    {
        return $this->monitoring;
    }

    /**
     * Execute le test
     */
    abstract public function execute();
}
