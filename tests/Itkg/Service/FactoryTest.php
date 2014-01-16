<?php

namespace Itkg\Test\Service;

use Itkg\Service\Factory;

/**
 * Class de test Factory
 *
 * @author Jean-Baptiste ROUSSEAU <jean-baptiste.rousseau@businessdecision.com>
 *
 * @package \Itkg\Service
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Factory;
        \Itkg::$config['MY_MOCK_SERVICE']['PARAMETERS'] = array(
            'location' => 'http://MOCK_IP/mockservice',
            'signature' => 'http://MOCK_IP/signature',
            'login' => 'MOCK_LOGIN',
            'password' => 'MOCK_PASSWORD',
            'mustunderstand' => 1,
            'timeout' => 10,
            'namespace' => 'http://MOCK_NAMESPACE',
            'wsdl' => '/MOCK_PATH_WSDL.wsdl'            
        );
        \Itkg::$config['MY_MOCK_SERVICE']['class'] = 'Itkg\Mock\Service';
        \Itkg::$config['MY_MOCK_SERVICE']['configuration'] = 'Itkg\Mock\Service\Configuration';
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Itkg\Service\Factory::getService
     * @todo   Implement testGetService().
     */
    public function testGetService()
    {
        $service = null;
        try {
            $this->object->getService($service);
            $this->fail('getService doit renvoyer une exception Itkg\Exception\NotFoundException');
        } catch(\Exception $e) {
             $this->assertEquals($e->getMessage(), "Le service  n'existe pas car la classe \Service\ n'est pas définie");
             $this->assertEquals('Itkg\Exception\NotFoundException', get_class($e));
        }
        $service = 'test';
        try {
            $this->object->getService($service);
            $this->fail('getService doit renvoyer une exception Itkg\Exception\NotFoundException');
        } catch(\Exception $e) {

             $this->assertEquals($e->getMessage(), "Le service test n'existe pas car la classe Test\Service\Test n'est pas définie");
             $this->assertEquals('Itkg\Exception\NotFoundException', get_class($e));
        }       
        $service = 'MY_MOCK_SERVICE';        
        try {
            $this->object->getService($service);
        } catch(\Exception $e) {
            $this->fail('getService ne doit pas renvoyer d\'exception');
        }
        $service = 'MY_MOCK_SERVICE';   
        \Itkg::$config['MY_MOCK_SERVICE']['configuration'] = null;         
        try {
            $this->object->getService($service);
            $this->fail('getService doit renvoyer une exception Itkg\Exception\NotFoundException');
        } catch(\Exception $e) {

            $this->assertEquals('Itkg\Exception\NotFoundException', get_class($e));
            $this->assertEquals($e->getMessage(), "La classe de configuration du service MY_MOCK_SERVICE n'existe pas car la classe \Configuration n'est pas définie");
        }
        $service = 'MY_MOCK_SERVICE';   
        \Itkg::$config['MY_MOCK_SERVICE']['configuration'] = 'Itkg\Mock\Service\Configuration';
        \Itkg::$config['MY_MOCK_SERVICE']['PARAMETERS'] = null;         
        try {
            $this->object->getService($service);
            $this->fail('getService doit renvoyer une exception Itkg\Exception\NotFoundException');
        } catch(\Exception $e) {
            $this->assertEquals('Itkg\Exception\NotFoundException', get_class($e));
            $this->assertEquals($e->getMessage(), "Aucun paramêtre n'est défini pour le service MY_MOCK_SERVICE. Veuillez définir \Itkg::\$config['MY_MOCK_SERVICE']['PARAMETERS']");
        }
    }
}