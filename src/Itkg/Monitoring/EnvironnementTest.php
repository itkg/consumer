<?php

namespace Itkg\Monitoring;

/**
 * Classe EnvironnementTest
 *
 * Test l'existence d'une variable d'environnement
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class EnvironnementTest extends Test
{
    /**
     * La clé à tester
     * @var string
     */
    protected $key;

    /**
     * Constructeur
     *
     * @param string $identifier
     * @param string $key Clé de $_ENV à tester
     */
    public function __construct($identifier, $key = '')
    {
        $this->key = $key;
        parent::__construct($identifier);
    }

    /**
     * Test l'existence d'une variable d'environnement
     *
     * @return boolean
     * @throws \Exception
     */
    public function execute()
    {
        if (!isset($_ENV[$this->key])) {
            throw new \Exception('La variable d\'environnement ' . $this->key . ' n\'existe pas');
        }

        return true;
    }

    /**
     * Getter key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Setter key
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }
}