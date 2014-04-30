<?php

namespace Itkg;

use Itkg\Service\Configuration as ServiceConfiguration;

/**
 * Classe abstraite représentant un Service
 * Un Service modélise plusieurs appels à un WS par le biais de méthodes
 * Chaque méthode peut contenir ses modèles / validateurs, loggers, incidentLoggers, identifiers
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @abstract
 * @package \Itkg
 */
abstract class Service
{
    /**
     * Configuration du service
     *
     * @var \Itkg\Service\Configuration
     */
    protected $configuration;

    /**
     * Paramètres utilisables au sein du service
     *
     * @var array
     */
    protected $parameters;

    /**
     * Début de l'appel en microsecondes
     *
     * @var string
     */
    protected $start;

    /**
     * Fin de l'appel en microsecondes
     *
     * @var string
     */
    protected $end;

    /**
     * Current Logger
     * @var \Itkg\Log\Logger
     */
    protected $logger;

    /**
     * Initialisation du service
     * Cette méthode est appelée automatiquement par la Factory
     */
    public function init()
    {
    }

    /**
     * Get la configuration du service
     *
     * @return \Itkg\Service\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Set la configuration du service
     *
     * @param \Itkg\Service\Configuration $configuration
     */
    public function setConfiguration(ServiceConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Getter start
     *
     * @return float
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Setter start
     *
     * @param float $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * Getter end
     *
     * @return float
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Setter end
     *
     * @param float $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * Retourne la durée de l'appel
     *
     * @param int $precision
     * @return float
     */
    public function getDuration($precision = 4)
    {
        return round(($this->end - $this->start), $precision);
    }

    /**
     * Appelle une méthode du service dynamiquement
     * Charge les modèles liées à la méthode
     * Appelle les méthodes preCall(), postCall() et logIncident() (si erreur)
     *
     * @param string $method Le nom de la méthode à appeler
     * @param array $aDatas Les datas à transmettre
     * @param boolean $bDebug Pour afficher le debug de l'appel
     *
     * @return mixed
     */
    public function call($method, $aDatas = array(), $bDebug = false)
    {
        $exception = null;
        $oResponse = null;
        $requestModel = $this->initRequestModelForCall($method, $aDatas);
        $responseModel = $this->initResponseModelForCall($method);
        /**
         * Appel preCall pour actions à réaliser avant l'execution de la méthode
         */
        $this->preCall($method, $requestModel, $aDatas);
        // Initialisation de l'appel
        $this->start = microtime(true);
        /**
         * Récupération du nom de la classe du ResponseModel
         */
        $responseModelClass = $this->configuration->getResponseModelClass($method);
        /**
         * Récupération du tableau de mapping
         */
        $mapping = $this->configuration->getMapping($method);
        /**
         * Appel de la méthode
         */
        try {
            $oResponse = $this->$method($requestModel, $responseModelClass, $mapping);
        } catch (\Exception $e) {
            $exception = $e;
            $this->logIncident($method, $exception, $aDatas);
        }
        // Fin de l'appel
        $this->end = microtime(true);
       
        $this->displayDebugForCall($oResponse, $method, $aDatas, $bDebug);
        /**
         * Retourne le modèle réponse après l'execution de traitements
         * post-appel
         */
        return $this->postCall($oResponse, $requestModel, $exception, $aDatas, $method);
    }
    
    /**
     * prépare le modèle de requête pour la méthode call
     *
     * @codeCoverageIgnore
     * @param string $method Le nom de la méthode à appeler
     * @param array $aDatas Les datas à transmettre
     *
     * @return mixed
     */
    protected function initRequestModelForCall($method, $aDatas)
    {
        /**
         * Création du modèle de requete
         */
        $requestModel = $this->configuration->getRequestModel($method);

        /**
         * Exception si le requestModel n'est pas défini
         */
        if (!$requestModel || !is_object($requestModel)) {
            throw new Exception\NotFoundException(
                sprintf('Le Request Model pour la méthode %s n\'est pas défini', $method)
            );
        }
        /**
         * Injection des paramètres dans le modèle
         */
        $requestModel->injectDatas($aDatas);

        /**
         * Validation du modèle
         * Jette une exception si le modèle n'est pas valide
         */
        if (!$requestModel->validate()) {
            throw new Exception\ValidationException(
                $requestModel->getErrors(),
                'Erreur lors de la validation du request model'
            );
        }
        return $requestModel;
    }
    
    /**
     * prépare le modèle de réponse pour la méthode call
     *
     * @codeCoverageIgnore
     * @param string $method Le nom de la méthode à appeler
     *
     * @return mixed
     */
    protected function initResponseModelForCall($method)
    {
        /**
         * Création du modèle de reponse
         */
        $responseModel = $this->configuration->getResponseModel($method);
        /**
         * Exception si le responseModel n'est pas défini
         */
        if (!$responseModel) {
            throw new Exception\NotFoundException(
                sprintf('Le Response Model pour la méthode %s n\'est pas défini', $method)
            );
        }
        return $responseModel;
    }
    /**
     * affiche le debug de la méthode Call
     *
     * @codeCoverageIgnore
     * @param object $oResponse La réponse à debuguer
     * @param string $method Le nom de la méthode à appeler
     * @param array $aDatas Les datas à transmettre
     * @param boolean $bDebug Pour afficher le debug de l'appel
     *
     * @return mixed
     */
    protected function displayDebugForCall($oResponse, $method, $aDatas, $bDebug)
    {
        /**
         * Affichage du debug
         */
        if ($bDebug) {
            echo '<br/><br/><strong>=============== Appel methode ' . $method . ' ================</strong><br/>';
            echo '<br/>--------------------------------- Paramètres ws ---------------------------------<br/>';
            $aParametres = $this->configuration->getParameters();
            if (is_array($aParametres) && !empty($aParametres)) {
                foreach ($aParametres as $key => $value) {
                    echo($key . ' : ' . $value . '<br/>');
                }
            }
            echo '<br/>--------------------------------- Données ws ---------------------------------<br/>';
            if (is_array($aDatas) && !empty($aDatas)) {
                foreach ($aDatas as $key => $value) {
                    echo($key . ' : ' . $value . '<br/>');
                }
            }
            echo '<br/>--------------------------------- Trame appel ---------------------------------<br/>';
            echo(htmlentities($this->client->__getLastRequest()) . '<br/>');
            echo '<br/>--------------------------------- Trame reponse ---------------------------------<br/>';
            echo(htmlentities($this->client->__getLastResponse()) . '<br/>');
            echo '<br/>--------------------------------- Reponse model ---------------------------------<br/>';
            echo '<pre>';
            var_dump($oResponse);
            echo '</pre>';
        }
    }

    /**
     * Methode executé avant l'appel de la méthode du Service
     * Permet par exemple un formatage des donn?es pass?es
     * ou un traitement sp?cifique
     *
     * @param string $method : la méthode appelée par call
     * @param object Model $oRequestModel
     * @param array $aDatas Les paramètres d'appel
     */
    public function preCall($method, $oRequestModel = null, array $aDatas = array())
    {
        $this->logger = $this->configuration->getLogger($method);

        $this->logger->init($this->configuration->getMethodIdentifierForLogger($method));
    }

    /**
     * Methode executée après l'appel de la méthode du Service
     * Permet par exemple la mise en place de logs spécifiques
     * ou le traitement de la réponse
     *
     * @param mixed $oResponse
     * @param \Itkg\Service\Model $oRequestModel
     * @param \Exception $exception
     * @param array $aDatas
     * @param string $method : la méthode appelée par call
     * @throws object
     * @return mixed $oResponse
     */
    public function postCall(
        $oResponse,
        $oRequestModel = null,
        \Exception $exception = null,
        array $aDatas = array(),
        $method = ''
    ) {
        $paramsLogs = array();
        $reponseTrame = "";
        $requestTrame = "";

        $this->getRequestAndResponseTrame($requestTrame, $reponseTrame);

        //on logue l'appel à la fin (postCall), pour avoir la trame d'appel (getLastRequest)
        $paramsLogs["appelRetour"] = "APPEL";
        $requestToLog = "";
        if ($oRequestModel) {
            $requestToLog = $oRequestModel->__toLog();
        }
        $this->logger->addInfo($requestToLog . $requestTrame, $paramsLogs);

        $paramsLogs["requestTime"] = $this->getDuration();
        if (is_object($exception)) {
            $this->logResponseKO($oRequestModel, $exception, $reponseTrame, $paramsLogs);
            /** @var $exception \Exception */
            throw $exception;
        } else {
            $this->logResponseOK($oResponse, $reponseTrame, $paramsLogs);
        }

        return $oResponse;
    }

    /**
     * log la réponse OK
     *
     * @codeCoverageIgnore
     * @param $oResponse
     * @param string $reponseTrame La trame de réponse SOAP
     * @param $paramsLogs
     * @internal param string $requestTrame La trame de requête SOAP
     */
    protected function logResponseOK($oResponse, $reponseTrame, &$paramsLogs)
    {
        $paramsLogs["appelRetour"] = "REPONSE OK";
        $sLogResponseModel = "";
        if (is_object($oResponse) && method_exists($oResponse, "__toLog")) {
            $sLogResponseModel = $oResponse->__toLog();
        }
        $this->logger->addInfo($sLogResponseModel . $reponseTrame, $paramsLogs);
    }

    /**
     * log la réponse KO
     *
     * @codeCoverageIgnore
     * @param $oRequestModel
     * @param $exception
     * @param string $reponseTrame La trame de réponse SOAP
     * @param $paramsLogs
     * @internal param string $requestTrame La trame de requête SOAP
     */
    protected function logResponseKO($oRequestModel, $exception, $reponseTrame, &$paramsLogs)
    {
        $paramsLogs["appelRetour"] = "REPONSE KO";
        $sLogRequestModel = "";
        if (is_object($oRequestModel) && method_exists($oRequestModel, "__toLog")) {
            $sLogRequestModel = ' - ' . $oRequestModel->__toLog();
        }
        $this->logger->addError('Erreur : ' . $exception->getMessage() . $sLogRequestModel . $reponseTrame, $paramsLogs);
    }
    
    /**
     * récupère la trame de réponse dans le cas d'un client SOAP'
     *
     * @codeCoverageIgnore
     * @param string $requestTrame La trame de requête SOAP
     * @param string $reponseTrame La trame de réponse SOAP
     */
    protected function getRequestAndResponseTrame(&$requestTrame, &$reponseTrame)
    {
        //récupération des trames dans le cas d'un client SOAP
        if ($this->configuration->getParameter("disableLogTrame") != "true"
            && (is_subclass_of($this->client, "SoapClient")
                || is_subclass_of($this->client, "Guzzle\\Http\\Client"))
        ) {
            $requestTrame = $this->client->__getLastRequest();
            if (strlen($requestTrame) > 0) {
                $requestTrame = str_replace(CHR(13) . CHR(10), "", $requestTrame);
                $requestTrame = " - " . $requestTrame;
            }
            $reponseTrame = $this->client->__getLastResponse();
            if (strlen($reponseTrame) > 0) {
                $reponseTrame = str_replace(CHR(13) . CHR(10), "", $reponseTrame);
                $reponseTrame = " - " . $reponseTrame;
            }
        }
    }

    /**
     * Getter parameters
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
     * En cas d'erreur levée dans la méthode call()
     * - ajoute les différentes données utilisées pour
     * l'appel dans l'exception pour qu'elles puissent être reprises
     * - vérifie si la méthode possède un logger d'incident, si c'est le cas,
     * le logger sera créé et écrira la trame de requète dans l'exception.
     * Si aucun logger n'est défini, aucune action sera effectué
     *
     * Ne sera géré que dans le cas d'une SoapException
     *
     * @param string $method
     * @param \Exception $e
     * @param array $aDatas
     */
    public function logIncident($method, \Exception $e, array $aDatas = array())
    {
        $logger = $this->configuration->getMethodIncidentLogger($method);

        if ($logger) {
            if ($e instanceof \Itkg\Soap\Exception\SoapException) {
                $e->setDatas($aDatas);
                $logger->addCritical($e->getTrame());
            }
        }
    }

    /**
     * Renvoie true si le service est accessible, false dans le cas contraire
     * Par défaut un service est accessible
     *
     * @return boolean
     */
    public function canAccess()
    {
        return true;
    }

    /**
     * Monitoring du service
     * A redéfinir pour chaque service monitoré
     */
    abstract public function monitor();

    /**
     * Cette méthode lance le monitoring du service
     * en faisant notamment appel à la méthode monitor
     */
    public function test()
    {
        $monitoring = new \Itkg\Monitoring();
        $monitoring->addService($this, 'monitor');
    }
}
