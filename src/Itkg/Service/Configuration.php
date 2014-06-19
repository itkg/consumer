<?php

namespace Itkg\Service;

use Itkg\Log\Factory as LogFactory;

/**
 * Classe générique de configuration d'un service
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @package \Itkg\Configuration
 */
abstract class Configuration
{
    /**
     * Paramètres de configuration
     *
     * @var array
     */
    protected $parameters;

    /**
     * Les différents modèles du service
     * Doit être défini par méthode de Service (identifié par le nom de la méthode)
     * @var array
     */
    protected $models;

    /**
     * Les différents loggers du service
     * Doit être défini par méthode de Service (identifié par le nom de la méthode)
     * @var array
     */
    protected $loggers;

    /**
     * Identifiant du service
     *
     * @var string
     */
    protected $identifier = '';


    /**
     * Identifiants des méthodes du service
     * Doit être défini par méthode de Service (identifié par le nom de la méthode)
     * @var array
     */
    protected $methodIdentifiers;

    /**
     * Logger d'incident
     * Si le logger n'est pas défini, la reprise sur incident n'aura pas lieu
     * Doit être défini par méthode de Service (identifié par le nom de la méthode)
     *
     * @var \Itkg\Log\Writer
     */
    protected $methodsIncidentLogger;

    /**
     * Permet l'initialisation de la configuration
     */
    public function init()
    {

    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
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
     * Get models
     *
     * @return array
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * Set models
     *
     * @param array $models
     */
    public function setModels(array $models)
    {
        $this->models = $models;
    }

    /**
     * Retourne un model défini pour un requete pour la méthode passée
     * en paramètre
     *
     * @param string $method
     *
     * @return \Itkg\Model|boolean
     */
    public function getRequestModel($method)
    {
        if (isset($this->models[$method]['request'])) {
            $model = new $this->models[$method]['request']['model'];
            if (isset($this->models[$method]['request']['validator'])) {
                $model->setValidator(new $this->models[$method]['request']['validator']);
            }
            $model->init();
            return $model;
        }
        return false;
    }

    /**
     * Retourne un model défini pour une réponse et pour la méthode passée
     * en paramètre
     *
     * @param string $method
     *
     * @return \Itkg\Model|boolean
     */
    public function getResponseModel($method)
    {
        if (isset($this->models[$method]['response'])) {
            $model = new $this->models[$method]['response']['model'];
            if (isset($this->models[$method]['response']['validator'])) {
                $model->setValidator(new $this->models[$method]['response']['validator']);
            }
            $model->init();
            return $model;
        }
        return false;
    }

    /**
     * Retourne le nom de la classe du model défini pour une réponse et pour la méthode passée
     * en paramètre
     *
     * @param string $method
     *
     * @return string|boolean
     */
    public function getResponseModelClass($method)
    {
        if (isset($this->models[$method]['response']['model'])) {
            return $this->models[$method]['response']['model'];
        }

        return false;
    }

    /**
     * Retourne un tableau de mapping de champs avec des classes datamap correspondantes
     *
     * @param string $method
     *
     * @return array
     */
    public function getMapping($method)
    {
        $aMapping = array();
        if (isset($this->models[$method]['response']['mapping'])) {
            $aMapping = $this->models[$method]['response']['mapping'];
        }
        return $aMapping;
    }

    /**
     * Ajoute la liste de paramètres à la liste courante
     *
     * @param array $aParameters
     */
    public function loadParameters(array $aParameters = array())
    {
        if (!is_array($this->parameters)) {
            $this->parameters = array();
        }
        $this->parameters = array_merge($this->parameters, $aParameters);
    }

    /**
     * Renvoie le logger défini pour la méthode passé en paramètre (avec le bon formatter)
     * Si aucun logger n'est défini pour la méthode on récupère celui du service
     * Si aucun logger n'est défini pour le service on récupère le logger par défaut
     *
     * @param string $method
     * @return \Itkg\Log\Logger
     */
    public function getLogger($method)
    {
        $logger = array();
        if (isset($this->loggers[$method])) {
            $logger = $this->loggers[$method];
        } else {
            if (isset($this->loggers['default'])) {
                $logger = $this->loggers['default'];
            }
        }

        // Renvoi du logger créé
        return LogFactory::getLogger($logger, $method);
    }

    /**
     * L'identifiant du service
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * L'identifiant du service utilisé par le logger
     *
     * @return string
     */
    public function getIdentifierForLogger()
    {
        return $this->identifier;
    }

    /**
     * L'identifiant du service utilisé par le monitoring
     *
     * @return string
     */
    public function getIdentifierForMonitoring()
    {
        return $this->identifier;
    }

    /**
     * retourne les Identifiants des méthodes du service
     *
     * @return array
     */
    public function getMethodIdentifiers()
    {
        return $this->methodIdentifiers;
    }


    /**
     * Set l'identifiant du service
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Retourne la trame identifiant la méthode du service
     * dont le nom est passée en paramètre
     *
     * @param string $method
     * @return string
     */
    public function getMethodIdentifier($method = '')
    {
        // Formatage de l'identifiant de la méthode préfixée par l'identifiant du service
        if (isset($this->methodIdentifiers[$method])) {
            return $this->getIdentifier() . ' - ' . $this->methodIdentifiers[$method];
        }

        return $this->getIdentifier();
    }

    /**
     * Retourne la trame utilisée par le logger identifiant la méthode du service
     * dont le nom est passée en paramètre
     *
     * @param string $method
     * @return string
     */
    public function getMethodIdentifierForLogger($method = '')
    {
        // Formatage de l'identifiant de la méthode préfixée par l'identifiant du service
        if (isset($this->methodIdentifiers[$method])) {
            return $this->getIdentifierForLogger() . ' - ' . $this->methodIdentifiers[$method];
        }

        return $this->getIdentifierForLogger();
    }

    /**
     * Renvoie true si l'écriture des trames dans les logs est activée, false dans le cas contraire
     * Par défaut l'écriture des trames dans les logs est activée
     *
     * @return boolean
     */
    public function logTrameEnabled()
    {
        if (isset($this->parameters['disableLogTrame'])) {
            return !$this->parameters['disableLogTrame'];
        }
        return true;
    }

    /**
     * Renvoie le logger défini pour la méthode passé en paramètre (avec le bon formatter)
     * Si aucun logger n'est défini pour la méthode on ne renvoie rien
     *
     * @param string $method
     * @return \Itkg\Log\Logger
     */
    public function getMethodIncidentLogger($method)
    {
        if (is_array($this->methodsIncidentLogger[$method])) {
            return LogFactory::getLogger($this->methodsIncidentLogger[$method], $method);
        }
        return null;
    }

    /**
     * Méthode appelée sur l'environnement de dev
     * Permet de charger des loggers particuliers pour un debug par exemple
     * ou toute autre configuration additionnelle
     *
     * Méthode appelée par défaut dans la Factory
     */
    public function loadDev()
    {
    }

    /**
     * Méthode appelée sur l'environnement de préproduction
     * Permet de charger des loggers particuliers pour un debug par exemple
     * ou toute autre configuration additionnelle
     *
     * Méthode appelée par défaut dans la Factory
     */
    public function loadPreprod()
    {
    }

    /**
     * Méthode appelée sur l'environnement de recette
     * Permet de charger des loggers particuliers pour un debug par exemple
     * ou toute autre configuration additionnelle
     *
     * Méthode appelée par défaut dans la Factory
     */
    public function loadRecette()
    {
    }

    /**
     * Méthode appelée sur l'environnement de production
     * Permet de charger des loggers particuliers pour un debug par exemple
     * ou toute autre configuration additionnelle
     *
     * Méthode appelée par défaut dans la Factory
     */
    public function loadProd()
    {
    }

    /**
     * Méthode appelée avant l'initialisation du service par la Factory.
     * Permet de surcharger la configuration
     *
     * @param string $serviceIdentifier Identifiant alphanumérique du service
     */
    public function override($serviceIdentifier)
    {
    }

    /**
     * Fonction permettant de détecter l'activation du webservice
     * Par défaut, un service est activé
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Fonction permettant de détecter la supervision du webservice
     * Par défaut, un service est supervisé (la méthode monitor()
     * est obligatoirement définie car abstraite dans la classe \Itkg\Service)
     *
     * @return boolean
     */
    public function isMonitored()
    {
        return true;
    }

    /**
     * Fonction permettant de détecter l'activation log d'appel au cache
     * Par défaut, un service est activé 
     * 
     * @return boolean
     */
    public function isCacheEnabled()
    {
        return TRUE;
    }

    /**
     * Fonction permettant de détecter l'activation des log si les WS sont débrayés
     * Par défaut, un service est activé 
     * 
     * @return boolean
     */
    public function isDisplayWsEnabled()
    {
        return TRUE;
    }
}
