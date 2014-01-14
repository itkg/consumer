<?php

namespace Itkg\Monitoring;

use Itkg\Mock\Monitoring\Test as MockTest;

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
class Test extends \PHPUnit_Framework_TestCase
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
        $this->object = new MockTest();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Itkg\Monitoring\Test::__construct
     * @covers Itkg\Monitoring\Test::getIdentifier
     */
    public function test__construct()
    {
        \Itkg\Monitoring::clear();
        
        $this->assertEquals('', $this->object->getIdentifier());
        $this->assertEquals(0, sizeof(\Itkg\Monitoring::getTests()));
        
        $this->object = new MockTest('IDENTIFIANT');
        $this->assertEquals('IDENTIFIANT', $this->object->getIdentifier());
        $this->assertEquals(1, sizeof(\Itkg\Monitoring::getTests()));
    }
     
    /**
     * @covers Itkg\Monitoring\Test::getIdentifier
     * @covers Itkg\Monitoring\Test::setIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertNotEquals('IDENTIFIER', $this->object->getIdentifier());
        $this->object->setIdentifier('IDENTIFIER');
        $this->assertEquals('IDENTIFIER', $this->object->getIdentifier());
    }
}
