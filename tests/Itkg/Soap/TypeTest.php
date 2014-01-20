<?php
namespace Itkg\Soap;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2014-01-16 at 18:57:46.
 */
class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SoapException
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Type("myname", "myvar", true);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    /**
     * @covers Itkg\Soap\Type::setName
     * @covers Itkg\Soap\Type::getName
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
     * @covers Itkg\Soap\Type::setVar
     * @covers Itkg\Soap\Type::getVar
     */
    public function testVar()
    {
        $this->object->setVar("testvar");
        $attr = \PHPUnit_Framework_Assert::readAttribute($this->object, 'var');
        $this->assertEquals("testvar", $attr);
        $name = $this->object->getVar();
        $this->assertEquals("testvar", $name);
    }      
    /**
     * @covers Itkg\Soap\Type::addType
     */
    public function testAddType()
    {
        $this->object->addType("testName", "var", true);
        $type = new Type("testName", "var", true);
        $attr = \PHPUnit_Framework_Assert::readAttribute($this->object, 'types');
        $this->assertEquals($type, $attr["var"]);
    }  
    
    
}