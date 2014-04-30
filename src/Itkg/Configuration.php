<?php

namespace Itkg;

use Itkg\Log\Factory as LogFactory;

/**
 * Classe de configuration
 *
 * Cette classe permet de définir les bases de configuration
 * utilisées par les différents composants Itkg
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class Configuration
{
    /**
     * Identifiant du batch
     *
     * @var string
     */
    protected $identifier;

    /**
     * Loggers associés au batch
     *
     * @var array
     */
    protected $loggers;

    /**
     * Notifiers associés au batch
     *
     * @var array
     */
    protected $notifiers;

    /**
     * Liste des paramètres de configuration du batch
     *
     * @var array
     */
    protected $parameters;

    /**
     * Permet l'initialisation de la configuration
     */
    public function init()
    {
        // On initialise les loggers créés sous forme de tableau
        $this->initLoggers();
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        if (!is_array($this->parameters)) {
            $this->parameters = array();
        }
        return $this->parameters;
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters = array())
    {
        $this->parameters = $parameters;
    }

    /**
     * Renvoi un paramètre par son nom ou false si le paramètre n'existe pas
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function getParameter($key)
    {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }
        return false;
    }

    /**
     * Ajoute la liste de paramètres à la liste courante
     *
     * @param array $parameters
     */
    public function loadParameters(array $parameters = array())
    {
        $this->parameters = array_merge($this->parameters, $parameters);
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
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Ajout d'un logger à la pile
     *
     * @param \Itkg\Log\Writer $logger
     */
    public function addLogger(\Itkg\Log\Logger $logger)
    {
        $this->getLoggers();
        $this->loggers[] = $logger;
    }

    /**
     * Formate la liste des loggers si ceux-ci sont sous forme de tableaux
     * et non d'objets
     */
    public function initLoggers()
    {
        foreach ($this->getLoggers() as $key => $handler) {
            $this->loggers[$key] = LogFactory::getLogger(array($handler));
        }
    }

    /**
     * Getter loggers
     *
     * @return array
     */
    public function getLoggers()
    {
        if (!is_array($this->loggers)) {
            $this->loggers = array();
        }

        return $this->loggers;
    }

    /**
     * Setter loggers
     *
     * @param array $loggers
     */
    public function setLoggers(array $loggers = array())
    {
        $this->loggers = $loggers;
    }

    /**
     * Ajoute un notifier à la pile
     *
     * @param \Itkg\Log\Notifiable $notifier
     */
    public function addNotifier(\Itkg\Log\Notifiable $notifier)
    {
        $this->getNotifiers();
        $this->notifiers[] = $notifier;
    }

    /**
     * Getter notifiers
     *
     * @return array
     */
    public function getNotifiers()
    {
        if (!is_array($this->notifiers)) {
            $this->notifiers = array();
        }

        return $this->notifiers;
    }

    /**
     * Setter notifiers
     *
     * @param array $notifiers
     */
    public function setNotifiers(array $notifiers = array())
    {
        $this->notifiers = $notifiers;
    }
}
