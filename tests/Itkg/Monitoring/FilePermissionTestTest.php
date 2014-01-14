<?php

namespace Itkg\Monitoring;

use Itkg\Monitoring\FilePermissionTest as MockTest;

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
class FilePermissionTestTest extends \PHPUnit_Framework_TestCase
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
        $this->object = new MockTest('');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Itkg\Monitoring\FilePermissionTest::__construct
     * @covers Itkg\Monitoring\FilePermissionTest::getIdentifier
     * @covers Itkg\Monitoring\FilePermissionTest::getPath
     * @covers Itkg\Monitoring\FilePermissionTest::getPermissions
     */
    public function test__construct()
    {
        \Itkg\Monitoring::clear();
        
        $this->assertEquals('', $this->object->getIdentifier());
        $this->assertEquals(0, sizeof(\Itkg\Monitoring::getTests()));
        $this->assertEquals('', $this->object->getPath());
        $this->assertEquals(array(), $this->object->getPermissions());
        
        $permissions = array('755', '777', '775');
        
        $this->object = new MockTest('IDENTIFIANT', __DIR__, $permissions);
        
        $this->assertEquals(__DIR__, $this->object->getPath());
        $this->assertEquals($permissions, $this->object->getPermissions());
        $this->assertEquals('IDENTIFIANT', $this->object->getIdentifier());
        $this->assertEquals(1, sizeof(\Itkg\Monitoring::getTests()));
    }
     
    /**
     * @covers Itkg\Monitoring\FilePermissionTest::getIdentifier
     * @covers Itkg\Monitoring\FilePermissionTest::setIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertNotEquals('IDENTIFIER', $this->object->getIdentifier());
        $this->object->setIdentifier('IDENTIFIER');
        $this->assertEquals('IDENTIFIER', $this->object->getIdentifier());
    }
    
    /**
     * @covers Itkg\Monitoring\FilePermissionTest::getPath
     * @covers Itkg\Monitoring\FilePermissionTest::setPath
     */
    public function testGetPath()
    {
        $this->object->setPath(__DIR__);
        $this->assertEquals(__DIR__, $this->object->getPath());
    }
    
    /**
     * @covers Itkg\Monitoring\FilePermissionTest::getPermissions
     * @covers Itkg\Monitoring\FilePermissionTest::setPermissions
     */
    public function testGetPermissions()
    {
        $permissions = array('777', '755');
        $this->object->setPermissions($permissions);
        $this->assertEquals($permissions, $this->object->getPermissions());
    }
    
    /**
     * @covers Itkg\Monitoring\FilePermissionTest::execute
     */
    public function testExecute()
    {
        $permissions = array('755', '777', '775');
        $this->object->setPermissions($permissions);
        $this->object->setPath(__DIR__);
        $this->assertTrue($this->object->execute());
        
        $this->setExpectedException('Exception');
        $permissions = array('000');
        $this->object->setPermissions($permissions);
        $this->object->execute();
    }
}
