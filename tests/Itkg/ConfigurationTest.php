<?php

namespace Itkg;

/**
 * Classe pour les test phpunit de la classe Itkg\Configuration
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 *
 * @package \Itkg
 * 
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**

     * @var Itkg\Configuration
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {         
      $this->object = new \Itkg\Configuration();
      $this->object->setIdentifier("[BULK BATCH]");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    /**
     * Get parameters
     *
     * @covers Itkg\Configuration::getParameters
     */
    public function testGetParameters()
    {
        $this->assertNotNull($this->object->getParameters());
    }

    /**
     * Set parameters
     *
     * @covers Itkg\Configuration::setParameters
     */
    public function testSetParameters()
    {
        $parameters = array('PARAMETER_ONE' => 'ONE');
        $this->object->setParameters($parameters);
        $this->assertEquals($parameters, $this->object->getParameters());
    }
    
    /**
     * Renvoi un paramètre par son nom ou false si le paramètre n'existe pas
     *
     * @covers Itkg\Configuration::getParameter
     */
    public function testGetParameter()
    {
        $parameters = array('PARAMETER_ONE' => 'ONE');
        $this->object->setParameters($parameters);
        
        $this->assertEquals('ONE', $this->object->getParameter('PARAMETER_ONE'));
        $this->assertEquals(false, $this->object->getParameter('UNKNOWN_PARAMETER'));
    }
    
    /**
     * Ajoute la liste de paramètres à la liste courante
     *
     * @covers Itkg\Configuration::loadParameters
     */
    public function testLoadParameters()
    {
        $parameters = array('PARAMETER_ONE' => 'ONE');
        $this->object->setParameters($parameters);
        
        $parametersTwo = array('PARAMETER_TWO' => 'TWO');
        $this->object->loadParameters($parametersTwo);
        
        $this->assertEquals(
            array_merge($parameters, $parametersTwo),
            $this->object->getParameters()
        );
    }
    
    /**
     * Getter identifier
     * 
     * @covers Itkg\Configuration::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertNotNull($this->object->getIdentifier());
    }
    
    /**
     * Setter identifier
     * 
     * @covers Itkg\Configuration::setIdentifier
     */
    public function testSetIdentifier()
    {
        $identifier = 'identifier';
        $this->object->setIdentifier($identifier);
        $this->assertEquals($identifier, $this->object->getIdentifier());
    }
    
    /**
     * Ajout d'un logger à la pile
     * 
     * @covers Itkg\Configuration::addLogger
     */
    public function testAddLogger()
    {
        $logger = \Itkg\Log\Factory::getWriter('echo');
        $nbLogger = sizeof($this->object->getLoggers());
        $this->object->addLogger($logger);
        $this->assertEquals(($nbLogger+1), sizeof($this->object->getLoggers()));
    }
    
    /**
     * Formate la liste des loggers si ceux-ci sont sous forme de tableaux
     * et non d'objets
     * 
     * @covers Itkg\Configuration::initLoggers
     */
    public function testInitLoggers()
    {
        $loggers = array(array('writer' => 'echo'));
        $this->object->setLoggers($loggers);
        
      
        $this->object->initLoggers();
        $finalLoggers = $this->object->getLoggers();
        $this->assertEquals(get_class($finalLoggers[0]), 'Itkg\Log\Writer\EchoWriter');
        
        $loggers = array(array('writer' => 'file'));
        $this->object->setLoggers($loggers);
        
        $this->object->initLoggers();
        $finalLoggers = $this->object->getLoggers();
        $this->assertEquals(get_class($finalLoggers[0]), 'Itkg\Log\Writer\FileWriter');
        
        
        $loggers = array(\Itkg\Log\Factory::getWriter('file'));
        $this->object->setLoggers($loggers);
        $this->object->initLoggers();
        $finalLoggers = $this->object->getLoggers();
        $this->assertEquals(get_class($finalLoggers[0]), 'Itkg\Log\Writer\FileWriter');
        
        $loggers = array();
        $this->object->setLoggers($loggers);
        $this->object->initLoggers();
        $this->assertEquals(array(), $this->object->getLoggers());
        
        $loggers = array(array());
        $this->object->setLoggers($loggers);
        $this->object->initLoggers();
        $finalLoggers = $this->object->getLoggers();
        $this->assertEquals(get_class($finalLoggers[0]), \Itkg::$config['LOG']['WRITERS'][\Itkg::$config['LOG']['DEFAULT_WRITER']]);
        
        $loggers = array(
            array(
                'writer' => 'file', 
                'formatter' => 'string',
                'parameters' => array(
                    'file' => __DIR__.'/log.log'
                )
            )
        );
        
        $this->object->setLoggers($loggers);
        $this->object->initLoggers();
        $finalLoggers = $this->object->getLoggers();
        $parameters = $finalLoggers[0]->getParameters();
        $this->assertEquals(get_class($finalLoggers[0]->getFormatter()), 'Itkg\Log\Formatter\StringFormatter');
        $this->assertEquals($parameters['file'], __DIR__.'/log.log');
        
    }
        
    /**
     * Formate la liste des loggers si ceux-ci sont sous forme de tableaux
     * et non d'objets
     * 
     * @covers Itkg\Configuration::initLoggers
     * @covers Itkg\Configuration::init
     */
    public function testInit()
    {
        $loggers = array(
            array(
                'writer' => 'file', 
                'formatter' => 'string',
                'parameters' => array(
                    'file' => __DIR__.'/log.log'
                )
            )
        );
        
        $this->object->setLoggers($loggers);
        $this->object->init();
        $finalLoggers = $this->object->getLoggers();
        $parameters = $finalLoggers[0]->getParameters();
        $this->assertEquals(get_class($finalLoggers[0]->getFormatter()), 'Itkg\Log\Formatter\StringFormatter');
        $this->assertEquals($parameters['file'], __DIR__.'/log.log');
    }
    /**
     * Getter loggers
     * 
     * @covers Itkg\Configuration::getLoggers
     */
    public function testGetLoggers()
    {
        $this->assertNotNull($this->object->getLoggers());
    }
    
    /**
     * Setter loggers
     * 
     * @covers Itkg\Configuration::setLoggers
     */
    public function testSetLoggers()
    {
        $loggers = array(array('writer' => 'file'));
        $this->object->setLoggers($loggers);
        
        $this->assertEquals($loggers, $this->object->getLoggers());
    }
    
    /**
     * Ajoute un notifier à la pile
     * 
     * @covers Itkg\Configuration::addNotifier
     */
    public function testAddNotifier()
    {
        $nbNotifier = sizeof($this->object->getNotifiers());
        $this->object->addNotifier(new \Itkg\Mock\MyNotifier());
        $this->assertEquals(($nbNotifier+1), sizeof($this->object->getNotifiers()));
    }
    
    /**
     * Getter notifiers
     * 
     * @covers Itkg\Configuration::getNotifiers
     */
    public function testGetNotifiers()
    {
        $this->assertNotNull($this->object->getNotifiers());
    }
    
    /**
     * Setter notifiers
     * 
     * @covers Itkg\Configuration::setNotifiers
     */
    public function testSetNotifiers()
    {
        $notifiers = array(new \Itkg\Mock\MyNotifier());
        $this->object->setNotifiers($notifiers);
        $this->assertEquals($notifiers, $this->object->getNotifiers());
    }
}