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
class MonitoringTest extends \PHPUnit_Framework_TestCase
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
      $this->object = new \Itkg\Monitoring();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Itkg\Monitoring::getStart
     * @covers Itkg\Monitoring::setStart
     */
    public function testStart()
    {
        $this->assertNull($this->object->getStart());
        $start = microtime(true);
        $this->object->setStart($start);
        $this->assertEquals($start, $this->object->getStart());
    }

    /**
     * @covers Itkg\Monitoring::getEnd
     * @covers Itkg\Monitoring::setEnd
     */
    public function testEnd()
    {
        $this->assertNull($this->object->getEnd());
        $end = microtime(true);
        $this->object->setEnd($end);
        $this->assertEquals($end, $this->object->getEnd());
    }
    
    /**
     * @covers Itkg\Monitoring::getDuration
     */
    public function testDuration()
    {
        $this->assertNull($this->object->getDuration());
        $this->object->setDuration(2);
        $this->assertEquals(2, $this->object->getDuration());
    }
    
    /**
     * @covers Itkg\Monitoring::getException
     * @covers Itkg\Monitoring::setException
     */
    public function testException()
    {
        $exception = new \Exception('mon exception');
        
        $this->assertNull($this->object->getException());
        $this->object->setException($exception);
        $this->assertEquals($exception, $this->object->getException());
    }
    
    /**
     * @covers Itkg\Monitoring::addService
     * @covers Itkg\Monitoring::clear
     * @covers Itkg\Monitoring::getTests
     * @covers Itkg\Monitoring::getDuration
     * @covers Itkg\Monitoring::getService
     * @covers Itkg\Monitoring::isWorking
     * @covers Itkg\Monitoring::getException
     */
    public function testAddService()
    {
        \Itkg\Monitoring::clear();
        $this->assertEquals(array(), \Itkg\Monitoring::getTests());
        $service = new \Itkg\Mock\Service();
        $configuration = new \Itkg\Mock\Service\Configuration();
        $configuration->setIdentifier('IDENTIFIER');
        $service->setConfiguration($configuration);
        $this->object->addService($service, 'monitor');
        $tests = \Itkg\Monitoring::getTests();
        $this->assertEquals($tests[0], $this->object);
        $this->assertEquals('IDENTIFIER', $this->object->getIdentifier());
        $duration = $this->object->getEnd() - $this->object->getStart();
        $this->assertEquals($duration, $this->object->getDuration());
        $this->assertEquals($service, $this->object->getService());
        $this->assertTrue($this->object->isWorking());
    }
    
    /**
     * @covers Itkg\Monitoring::addTest
     * @covers Itkg\Monitoring::clear
     * @covers Itkg\Monitoring\Test::getMonitoring
     */
    public function testAddTest()
    {
        \Itkg\Monitoring::clear();
        $this->assertEquals(array(), \Itkg\Monitoring::getTests());
        $test = new \Itkg\Monitoring\EnvironnementTest('identifier', 'TEST');
        $tests = \Itkg\Monitoring::getTests();
        $this->assertEquals(1, sizeof($tests));
        $this->object = $test->getMonitoring();
        $this->assertEquals('identifier', $this->object->getIdentifier());
        $duration = $this->object->getEnd() - $this->object->getStart();
        
        $this->assertEquals($duration, $this->object->getDuration());
        $this->assertEquals($test, $this->object->getTest());
        $this->assertEquals($duration, $this->object->getDuration());
        $this->assertFalse($this->object->isWorking());
        $this->assertNotNull($this->object->getException());
    }
}
