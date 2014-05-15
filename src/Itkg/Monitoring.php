<?php

namespace Itkg;

use Itkg\Log\Logger;
use Itkg\Monitoring\Test;

/**
 * Classe de monitoring de Service
 * Cette classe permet le monitoring de différents services et effectue
 * un rapport global
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Cl�ment GUINET <clement.guinet@businessdecision.com>
 *
 * @abstract
 * @package \Itkg
 */
class Monitoring
{

    /**
     * Les monitorings courants
     *
     * @staticvar
     * @var array
     */
    protected static $tests;

    /**
     * Tests report
     *
     * @var string
     */
    protected static $report;

    /**
     * Les loggers courants
     *
     * @staticvar
     * @var array
     */
    protected static $loggers;

    /**
     * Le début du test
     * @var int
     */
    protected $start;

    /**
     * La fin du test
     * @var int
     */
    protected $end;

    /**
     * L'exception si elle a été levée par le test
     * @var \Exception
     */
    protected $exception;

    /**
     * L'état du test
     * @var boolean
     */
    protected $working;

    /**
     * La durée du test
     * @var int
     */
    protected $duration;

    /**
     * Identifiant du test
     * @var string
     */
    protected $identifier;

    /**
     * Monitored service
     *
     * @var Service
     */
    protected $service;

    /**
     * Test courant
     *
     * @var \Itkg\Monitoring\Test
     */
    protected $test;

    /**
     * Get le début du test
     *
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get la fin du test
     *
     * @return int
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Get l'exception si une exception a été levée par le test
     *
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Get la dur�e du test
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set le résultat du test
     *
     * @return boolean
     */
    public function isWorking()
    {
        return $this->working;
    }

    /**
     * Set le début du test
     *
     * @param int $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * Set la fin du test
     *
     * @param int $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * Set la durée du test
     *
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * Set l'exception associée
     *
     * @param \Exception $exception
     */
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * Ajoute un logger à la pile courante
     *
     * @static
     * @param \Itkg\Log\Writer $logger
     */
    public static function addLogger(Logger $logger, $id)
    {
        self::$loggers[$id] = $logger;
    }

    /**
     * Effectue le monitoring du service et ajoute le monitoring à la pile existante
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Effectue le monitoring du service et ajoute le monitoring à la pile existante
     *
     * @param \Itkg\Service $service
     * @param string $method
     */
    public function addService(\Itkg\Service $service, $method)
    {
        $response = null;

        if ($service->getConfiguration()->isMonitored()) {
            // Initialisation des attributs de monitoring + lancement du test et traitement
            $response = $this->execute($service, $method);
            $service = $this->postExecute($service, $response);
        }
        $this->identifier = $service->getConfiguration()->getIdentifierForMonitoring();
        $this->service = $service;
        self::$tests[] = $this;
    }

    /**
     * Execute service monitoring method
     */
    protected function execute(Service $service, $method)
    {
        $this->start = microtime(true);
        $oResponse = null;
        try {
            $service->preCall($method);

            $oResponse = $service->$method();
            $this->working = true;

        } catch (\Exception $e) {
            $this->exception = $e;
            $this->working = false;
        }

        return $oResponse;
    }

    /**
     * Some actions after service execute
     *
     * @param Service $service
     * @param mixed $response
     */
    protected function postExecute(Service $service, $response)
    {
        $this->end = microtime(true);
        //pour logguer les appels monitoring
        try {
            $service->setStart($this->start);
            $service->setEnd($this->end);
            $service->postCall($response, null, $this->exception);
        } catch (\Exception $e) {
            // on ne fait rien dans le cas du monitoring
        }

        $this->duration = $this->end - $this->start;

        return $service;
    }

    /**
     * Getter service
     * Retourne le service courant
     *
     * @return \Itkg\Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Getter test
     * Retourne le test courant
     *
     * @return \Itkg\Monitoring\Test
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * Effectue le test et ajoute le monitoring à la pile existante
     *
     * @param \Itkg\Monitoring\Test $test
     */
    public function addTest(Test $test)
    {
        $this->start = microtime(true);
        // Initialisation des attributs de monitoring + lancement du test et traitement

        $this->executeTest($test);

        $this->end = microtime(true);
        $this->duration = $this->end - $this->start;
        $this->identifier = $test->getIdentifier();
        self::$tests[] = $this;
    }

    /**
     * Execute a test
     *
     * @param Test $test
     */
    protected function executeTest(Test $test)
    {
        $this->test = $test;

        try {
            $test->execute();
            $this->working = true;

        } catch (\Exception $e) {

            $this->exception = $e;
            $this->working = false;
        }
    }

    /**
     * Initialise les tests
     */
    public static function clear()
    {
        // Initialisation des tests
        self::$tests = array();
        self::$report = '';
    }

    /**
     *
     * @param string $work Message si service OK
     * @param string $fail Message si service KO
     * @param string $generalWork Message si general OK
     * @param string $generalFail Message si general KO
     */
    public static function logReport(
        $work = 'OK',
        $fail = 'KO',
        $generalWork = '[GLOBAL : OKSFR]',
        $generalFail = '[GLOBAL : KOSFR]'
    ) {
        //Etat général
        if (self::isUP()) {
            self::$report .= '<br />' . $generalWork;
        } else {
            self::$report .= '<br />' . $generalFail;
        }

        self::log(self::$report);
    }

    /**
     * Tests stack is OK ?
     *
     * @return boolean
     */
    public static function isUP()
    {
        $working = true;
        // Log des rapports
        if (is_array(self::$tests)) {
            foreach (self::$tests as $test) {
                self::$report .= self::getReportForTest($test);
                if (!$test->isWorking()) {
                    $working = false;
                }
            }
        }

        return $working;
    }

    /**
     * Create log
     *
     * @param $report
     */
    public static function log($report)
    {
        if (is_array(self::$loggers)) {

            foreach (self::$loggers as $index => $logger) {
                // Les balises html ne s'affichent que dans le cas d'un echo
                if ($index == 'echo') {
                    $logger->addInfo($report);
                } else {
                    $report = str_replace('<br />', "\r\n", $report);
                    $report = strip_tags($report);
                    $logger->addInfo($report);
                }
            }
        }
    }

    /**
     * @param Monitoring $test
     */
    public static function getReportForTest(Monitoring $test, $work = 'OK', $fail = 'KO')
    {
        $serviceConfiguration = null;
        if ($test->getService()) {
            $serviceConfiguration = $test->getService()->getConfiguration();
        }
        if ($serviceConfiguration && !$serviceConfiguration->isMonitored()) {
            //si le service n'est pas supervisé
            return sprintf(
                '<span class="libelle nomon">%s (non supervis&eacute;)</span><br /><br />',
                $test->getIdentifier()
            );
        }

        //si le service est supervisé
        $disabled = '';
        if ($serviceConfiguration && !$serviceConfiguration->isEnabled()) {
            $disabled = 'disabled';
        }
		$class = ($test->isWorking()) ? 'working' : 'error';
        $e = $test->getException();
        return sprintf(
            '<span class="libelle %s %s">%s</span><br /><span class="%s %s">%s (%s sec) %s</span><br />',
	        $class,
	        $disabled,
            $test->getIdentifier(),
	        $class,
	        $disabled,
	        ($test->isWorking()) ? $work : $fail,
            number_format($test->getDuration(), 4),
            (!empty($e) ? (" - " . $e->getMessage()) : "")
        );
    }

    /**
     * Getter tests
     *
     * @static
     * @return array
     */
    public static function getTests()
    {
        if (!is_array(self::$tests)) {
            self::$tests = array();
        }
        return self::$tests;
    }
}
