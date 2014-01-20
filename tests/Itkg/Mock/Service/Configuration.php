<?php

namespace Itkg\Mock\Service;

use Itkg\Service\Configuration as BaseConfiguration;

/**
 * Implementation de Service (Mock)
 *
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @package \Itkg\Mock
 */
class Configuration extends BaseConfiguration
{
    public function __construct()
    {
        $this->methodIdentifiers = array(
            'test' => 'test'
        );        
        
        $this->loggers = array(
            'test' => array(
                'writer' => 'echo',
                'formatter' => 'simple'
            )
        );
        
        $this->models = array(
            'test' => array(
                'request' => array(
                    'model' => 'Itkg\Mock\Service\Model',
                    'validator' => 'Itkg\Mock\Service\Validator',
                 ),
                 'response' => array(
                    'model' => 'Itkg\Mock\Service\Model',
                    'validator' => 'Itkg\Mock\Service\Validator',
                 )
            )
        );
    }
    
    public function test()
    {
        
    }
    public function setMethodsIncidentLogger($methodsIncidentLogger)
    {
        $this->methodsIncidentLogger = $methodsIncidentLogger;
    }
}
