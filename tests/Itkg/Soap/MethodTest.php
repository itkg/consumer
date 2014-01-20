<?php


namespace Itkg\Soap;

use Itkg\Soap\Method;

/**
 * Class Client
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class MethodTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \Itkg\Soap\Method
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {         
      $this->object = new \Itkg\Soap\Method("testname", "String", "array");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    /**
     * @covers Itkg\Soap\Method::setName
     * @covers Itkg\Soap\Method::getName
     */
    public function testName()
    {
        $this->object->setName("testName");
        $attr = \PHPUnit_Framework_Assert::readAttribute($this->object, 'name');
        $this->assertEquals("testName", $attr);
        $name = $this->object->getName();
        $this->assertEquals("testName", $name);
    }     
    /**
     * @covers Itkg\Soap\Method::setReturnType
     * @covers Itkg\Soap\Method::getReturnType
     */
    public function testReturnType()
    {
        $this->object->setReturnType("ReturnType");
        $attr = \PHPUnit_Framework_Assert::readAttribute($this->object, 'returnType');
        $this->assertEquals("ReturnType", $attr);
        $name = $this->object->getReturnType();
        $this->assertEquals("ReturnType", $name);
    } 
    /**
     * @covers Itkg\Soap\Method::setParameterType
     * @covers Itkg\Soap\Method::getParameterType
     */
    public function testParameterType()
    {
        $this->object->setParameterType("parameterType");
        $attr = \PHPUnit_Framework_Assert::readAttribute($this->object, 'parameterType');
        $this->assertEquals("parameterType", $attr);
        $name = $this->object->getParameterType();
        $this->assertEquals("parameterType", $name);
    }    
}