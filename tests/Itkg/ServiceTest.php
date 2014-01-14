<?php

namespace Itkg;

/**
 * Classe pour les test phpunit pour la classe Service
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @package \Itkg
 * 
 */
class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Itkg\Mock\Service
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {         
      $this->object = new \Itkg\Mock\Service();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Itkg\Service::call
     * @todo   Implement testCall().
     */
    public function testCall()
    {
              
    }

    /**
     * @covers Itkg\Service::preCall
     * @todo   Implement testPreCall().
     */
    public function testPreCall()
    {
     
    }

    /**
     * @covers Itkg\Service::postCall
     * @todo   Implement testPostCall().
     */
    public function testPostCall()
    {
    }

    /**
     * @covers Itkg\Service::setParameters
     * @covers Itkg\Service::getParameters
     */
    public function testParameters()
    {
        $this->assertEquals(array(), $this->object->getParameters());
        $parameters = array('one', 'two');
        $this->object->setParameters($parameters);
        $this->assertEquals($parameters, $this->object->getParameters());
    }

    /**
     * @covers Itkg\Service::getStart
     * @covers Itkg\Service::setStart
     */
    public function testStart()
    {
        $this->assertNull($this->object->getStart());
        $start = microtime(true);
        $this->object->setStart($start);
        $this->assertEquals($start, $this->object->getStart());
    }

    /**
     * @covers Itkg\Service::getEnd
     * @covers Itkg\Service::setEnd
     */
    public function testEnd()
    {
        $this->assertNull($this->object->getEnd());
        $end = microtime(true);
        $this->object->setEnd($end);
        $this->assertEquals($end, $this->object->getEnd());
    }
    
    /**
     * @covers Itkg\Service::getDuration
     */
    public function testDuration()
    {
        $start = microtime(true);
        sleep(0.75);
        $end = microtime(true);
        
        $this->object->setStart($start);
        $this->object->setEnd($end);
        
        $this->assertEquals(round(($end-$start),4), $this->object->getDuration());
        $this->assertEquals(round(($end-$start),6), $this->object->getDuration(6));
        
    }
    
    /**
     * @covers Itkg\Service::getConfiguration
     * @covers Itkg\Service::setConfiguration
     */
    public function testConfiguration()
    {
        //GL : mise en commentaire de l'assertion suivante car elle n'est valable 
        //que pour la classe Mock, mais pas pour les autres classes étendant 
        //la classe Service
        //$this->assertNull($this->object->getConfiguration());
        $configuration = new \Itkg\Mock\Service\Configuration();
        $this->object->setConfiguration($configuration);
        $this->assertEquals($configuration, $this->object->getConfiguration());
    }
    
    /**
     * @covers Itkg\Service::logIncident
     * @todo   Implement testLogIncident().
     */
    public function testLogIncident()
    {
    }
    
    /**
     * @covers \Itkg\Service::monitor
     */
    public function testMonitor()
    {
        try {
            $this->object->monitor();
        } catch (Exception $e) {
            $this->fail($e->getMessage());
            return;
        }
        $this->assertTrue(true);
       
    }
    
    /**
     * @covers \Itkg\Service::canAccess
     */
    public function testCanAccess()
    {
        $this->assertTrue($this->object->canAccess());
    }
}
