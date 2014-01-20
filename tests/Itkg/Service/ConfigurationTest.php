<?php

namespace Itkg\Service;

use Itkg\Mock\Service\Configuration;
use \ReflectionClass;

/**
* Classe pour les  phpunit pour la classe Service\Configuration
*
* @author Pascal DENIS <pascal.denis@businessdecision.com>
* @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
* @author Clément GUINET <clement.guinet@businessdecision.com>
*
* @package Itkg
* @subpackage Service
* 
*/
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Configuration
     */
    protected $object;
    
    /**
     * nom de la classe du service correspondant
     * @var string
     */
    protected $serviceClassName = 'Itkg\Mock\Service';
    
    /**
     * nom des classes des request du service correspondant, par méthode
     * @var string[]
     */
    protected $aRequestModelClassName = array(
        'test'=>'Itkg\Mock\Service\Model',
    );
    
    /**
     * nom des classes des response du service correspondant, par méthode
     * @var string[]
     */
    protected $aResponseModelClassName = array(
        'test'=>'Itkg\Mock\Service\Model',
    );
    
    /**
     * nom des méthodes à exclure des tests d'existence dans la config
     * (= toutes méthodes autres que celles qui appellent les webservices)
     * @var string[]
     */
    protected $aExcludedMethods = array();
    
    /**
     * 
     * @var \ReflectionClass 
     * permet de comparer la configuration et la classe du service correspondant
     */
    protected $serviceClass;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Configuration();
        $this->serviceClass = new ReflectionClass($this->serviceClassName);
    }

    /**
     * @covers \Itkg\Service\Configuration::getMethodIdentifiers
     */
    public function testGetMethodIdentifiers()
    {
        $this->assertNotNull($this->object->getMethodIdentifiers());
    }
    /**
     * 
     * @covers Itkg\Service\Configuration::getResponseModelClass
     */
    public function testGetResponseModelClass()
    {
        $method = null;
        $this->assertFalse($this->object->getResponseModelClass($method));
        $models = array();
        $models['tests']['response']['model'] = "test2";
        $this->object->setModels($models);
        $method = 'tests';
        $this->assertEquals("test2", $this->object->getResponseModelClass($method));
    }
    /**
     * 
     * @covers Itkg\Service\Configuration::getMapping
     */
    public function testGetMapping()
    {
        $method = null;
        $this->assertInternalType("array", $this->object->getMapping($method));
        $this->assertTrue(count($this->object->getMapping($method))==0);
        $models = array();
        $models['tests']['response']['mapping'] = "test2";
        $this->object->setModels($models);
        $method = 'tests';
        $this->assertEquals("test2", $this->object->getMapping($method));
    }   
    /**
     * 
     * @covers Itkg\Service\Configuration::getMethodIncidentLogger
     */
    public function testGetMethodIncidentLogger()
    {
        $method = null;
        $this->assertNull($this->object->getMethodIncidentLogger($method));
        $attr = \PHPUnit_Framework_Assert::readAttribute($this->object, 'loggers');
        $methodsIncidentLogger = array();
        $method = "tests";
        $methodsIncidentLogger['tests']['writer']="testWritter" ;
        $methodsIncidentLogger['tests']['formatter']="testFormatter" ;
        $methodsIncidentLogger['tests']['parameters']=array('one', 'two');
        \Itkg\Log::$config['FORMATTERS']['testFormatter'] = "\Itkg\Log\Formatter\StringFormatter";
        \Itkg\Log::$config['WRITERS']['testWritter'] = "\Itkg\Log\Writer\EchoWriter";
        $this->object->setMethodsIncidentLogger($methodsIncidentLogger);
        $this->assertNotNull($this->object->getMethodIncidentLogger($method));
    }      
    
    
    
    /**
     * @covers \Itkg\Service\Configuration::__construct
     */
/*    public function test__construct()
    {
        $oConf = new Configuration();
        $this->assertEquals($oConf, $this->object);
    }
  */  
    /**
     * @covers \Itkg\Service\Configuration::getParameters
     */
    public function testGetParameters()
    {
        $parameters = array('login' => 'login');
        $this->object->setParameters($parameters);
        $this->assertEquals(1, count($this->object->getParameters()));
        $compareTo = $this->object->getParameters();
        $this->assertTrue(isset($compareTo['login']));
    }


    /**
     * @covers \Itkg\Service\Configuration::setParameters
     */
    public function testSetParameters()
    {
        $parameters = array('login' => 'login');
        $this->object->setParameters($parameters);
        $this->assertEquals(1, count($this->object->getParameters()));
        $this->assertEquals($parameters, $this->object->getParameters());
    }

    /**
     * @covers \Itkg\Service\Configuration::getParameter
     */
    public function testGetParameter()
    {
        $parameters = array('login' => 'login');
        $this->object->setParameters($parameters);
        $this->assertEquals('login', $this->object->getParameter('login'));
        $this->assertFalse($this->object->getParameter('unvalid'));
    }

    /**
     * @covers \Itkg\Service\Configuration::getModels
     */
    public function testGetModels()
    {
        $this->assertNotNull($this->object->getModels());
    }

    /**
     * @covers \Itkg\Service\Configuration::setModels
     */
    public function testSetModels()
    {
        $this->assertNotNull($this->object->getModels());
        $this->object->setModels(array());
        $this->assertEquals(0, sizeof($this->object->getModels()));
    }

    /**
     * @covers \Itkg\Service\Configuration::getRequestModel
     */
    public function testGetRequestModel()
    {
        foreach($this->object->getMethodIdentifiers() as $methodName=>$methodDesc){
            if(!in_array($methodName,$this->aExcludedMethods)){
                $model = $this->object->getRequestModel($methodName);
                $this->assertEquals($this->aRequestModelClassName[$methodName], get_class($model));
            }
        }
        $model = $this->object->getRequestModel('unknown');
        $this->assertFalse($model);
    }

    /**
     * @covers \Itkg\Service\Configuration::getResponseModel
     */
    public function testGetResponseModel()
    {
        foreach($this->object->getMethodIdentifiers() as $methodName=>$methodDesc){
            if(!in_array($methodName,$this->aExcludedMethods)){
                $model = $this->object->getResponseModel($methodName);
                $this->assertEquals($this->aResponseModelClassName[$methodName], get_class($model));
            }
        }
        $model = $this->object->getResponseModel('unknown');
        $this->assertFalse($model);
    }

    /**
     * @covers \Itkg\Service\Configuration::loadParameters
     */
    public function testLoadParameters()
    {
        $nbParameters = count($this->object->getParameters());
        $parameters = array('login' => 'login');
        $this->object->loadParameters($parameters);
        $this->assertEquals($nbParameters+1, count($this->object->getParameters()));
        $parameters = array('password' => 'password');
        $this->object->loadParameters($parameters);
        $this->assertEquals($nbParameters+2, count($this->object->getParameters()));
        $this->object->loadParameters($parameters);
        $this->assertEquals($nbParameters+2, count($this->object->getParameters()));
    }

    /**
     * @covers \Itkg\Service\Configuration::getLogger
     */
    public function testGetLogger()
    {
        foreach($this->object->getMethodIdentifiers() as $methodName=>$methodDesc){
            if(!in_array($methodName,$this->aExcludedMethods)){
                $this->assertNotNull($this->object->getLogger($methodName));
            }
        } 
    }

    /**
     * @covers \Itkg\Service\Configuration::getIdentifier
     */
    public function testGetIdentifier()
    {
        $identifier = 'identifier';
        $this->object->setIdentifier($identifier);
        $this->assertEquals($identifier, $this->object->getIdentifier());
    }


    /**
     * @covers \Itkg\Service\Configuration::setIdentifier
     */
    public function testSetIdentifier()
    {
        $identifier = 'identifier';
        $this->object->setIdentifier($identifier);
        $this->assertEquals($identifier, $this->object->getIdentifier());
    }

    /**
     * @covers \Itkg\Service\Configuration::getMethodIdentifier
     */
    public function testGetMethodIdentifier()
    {
        $identifier = 'identifier';
        $this->object->setIdentifier($identifier);
        foreach($this->object->getMethodIdentifiers() as $methodName=>$methodDesc){
            if(!in_array($methodName,$this->aExcludedMethods)){
                $aMethodIdentifier = explode(' - ', $this->object->getMethodIdentifier($methodName));
                $this->assertEquals($identifier, trim($aMethodIdentifier[0]));
                $this->assertEquals(2, count($aMethodIdentifier));
                $this->assertEquals($methodDesc, trim($aMethodIdentifier[1]));
            }
        }
    }

    /**
     * @covers \Itkg\Service\Configuration::loadDev
     */
    public function testLoadDev()
    {
        $this->object->loadDev();
    }

    /**
     * @covers \Itkg\Service\Configuration::loadPreprod
     */
    public function testLoadPreprod()
    {
        $this->object->loadPreprod();
    }

    /**
     * @covers \Itkg\Service\Configuration::loadRecette
     */
    public function testLoadRecette()
    {
        $this->object->loadRecette();
    }

    /**
     * @covers \Itkg\Service\Configuration::loadProd
     */
    public function testLoadProd()
    {
        $this->object->loadProd();
    }
    
    /**
     * @covers \Itkg\Service\Configuration::isEnabled
     */
    public function testIsEnabled()
    {
        $this->assertTrue($this->object->isEnabled());
    }    
    /**
     * @covers \Itkg\Service\Configuration::isMonitored
     */
    public function testIsMonitored()
    {
        $this->assertTrue($this->object->isMonitored());
    }       
    
     /**
     * @covers \Itkg\Service\Configuration::init
     */
    public function testInit()
    {
        
        //TODO: verif existence des classes définies dans la configuration (mapping, validators, response, request)
        
        foreach($this->object->getModels() as $methodName => $aModel){
            if(!in_array($methodName,$this->aExcludedMethods)){
                $requestClassName = $aModel['request']['model'];
                $responseClassName = $aModel['response']['model'];
                $requestValidatorClassName = $aModel['request']['validator'];
                $responseValidatorClassName = $aModel['response']['validator'];

                $this->assertTrue(class_exists($requestClassName),'la classe request pour '.$methodName.' n\'existe pas');
                $this->assertTrue(class_exists($responseClassName),'la classe response pour '.$methodName.' n\'existe pas');
                $this->assertTrue(class_exists($requestValidatorClassName),'la classe request validator pour '.$methodName.' n\'existe pas');
                $this->assertTrue(class_exists($responseValidatorClassName),'la classe response validator pour '.$methodName.' n\'existe pas');

                foreach($this->object->getMapping($methodName) as $element => $mappedClassName){
                        $this->assertTrue(class_exists($mappedClassName),'la classe '.$mappedClassName.' n\'existe pas');
                    //TODO : verif existence des champs mappés $element, dans le wsdl
                }
            }
        }
        
        $aModels = $this->object->getModels();
        foreach($this->object->getMethodIdentifiers() as $methodName=>$methodDesc){
            if(!in_array($methodName,$this->aExcludedMethods)){
                //verification de l'existence des méthodes définies dans la conf
                $this->assertTrue($this->serviceClass->hasMethod($methodName),'La méthode '.$methodName.' n\'est pas définie dans la classe '.$this->serviceClassName);
                $this->assertTrue(isset($aModels[$methodName]), 'Le modèle pour la méthode '.$methodName. 'n\'a pas été défini dans la configuration du service '.$this->serviceClassName);
            }
        }               
    }
}
