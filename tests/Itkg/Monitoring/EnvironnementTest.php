<?php

namespace Itkg\Monitoring;

use Itkg\Monitoring\EnvironnementTest as MockTest;

/**
 * Classe pour les test phpunit pour la classe Factory
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @package \Itkg
 * 
 */
class EnvironnementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Writer
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new MockTest('', '');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Itkg\Monitoring\EnvironnementTest::__construct
     * @covers Itkg\Monitoring\EnvironnementTest::getIdentifier
     * @covers Itkg\Monitoring\EnvironnementTest::getKey
     */
    public function test__construct()
    {
        \Itkg\Monitoring::clear();
        
        $this->assertEquals('', $this->object->getIdentifier());
        $this->assertEquals('', $this->object->getKey());
        $this->assertEquals(0, sizeof(\Itkg\Monitoring::getTests()));
        
        $this->object = new MockTest('IDENTIFIANT', 'AKEY');
        $this->assertEquals('IDENTIFIANT', $this->object->getIdentifier());
        $this->assertEquals('AKEY', $this->object->getKey());
        $this->assertEquals(1, sizeof(\Itkg\Monitoring::getTests()));
    }
     
    /**
     * @covers Itkg\Monitoring\EnvironnementTest::getIdentifier
     * @covers Itkg\Monitoring\EnvironnementTest::setIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertNotEquals('IDENTIFIER', $this->object->getIdentifier());
        $this->object->setIdentifier('IDENTIFIER');
        $this->assertEquals('IDENTIFIER', $this->object->getIdentifier());
    }
    
    /**
     * @covers Itkg\Monitoring\EnvironnementTest::getKey
     * @covers Itkg\Monitoring\EnvironnementTest::setKey
     */
    public function testGetKey()
    {
        $this->assertNotEquals('KEY', $this->object->getKey());
        $this->object->setKey('KEY');
        $this->assertEquals('KEY', $this->object->getKey());
    }
    
    /**
     * @covers Itkg\Monitoring\EnvironnementTest::execute
     */
    public function testExecute()
    {
        $_ENV['KEY'] = 'VALUE';
        $this->object->setKey('KEY');
        
        $this->assertEquals(true, $this->object->execute());
        $this->object->setKey('UNKNOWN');
        
        $this->setExpectedException('Exception');
        $this->object->execute();
    }
}