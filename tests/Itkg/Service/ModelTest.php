<?php
namespace Itkg\Service;

use Itkg\Mock\Service\Model;
use Itkg\Mock\Service\Validator;

/**
 * Class de test Model
 *
 * @author Jean-Baptiste ROUSSEAU <jean-baptiste.rousseau@businessdecision.com>
 *
 * @package \Itkg\Service
 */
class ModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    protected $object;
    protected $datas;
    protected $validator;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Model;
        $this->datas = array("test","test2");
        $this->validator = new Validator();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Itkg\Service\Model::getValidator
     */
    public function testGetValidator()
    {
        $this->object->setValidator($this->validator);
        $this->assertEquals($this->object->getValidator(), $this->validator);
    }

    /**
     * @covers Itkg\Service\Model::setValidator
     */
    public function testSetValidator()
    {
        $this->object->setValidator($this->validator);
        $this->assertEquals($this->validator, $this->object->validator);
    }

    /**
     * @covers Itkg\Service\Model::validate
     */
    public function testValidate()
    {
        $this->object = new Model;
        $this->assertTrue($this->object->validate());
        $this->object->setValidator($this->validator);
        $this->assertFalse($this->object->validate());
    }

    /**
     * @covers Itkg\Service\Model::init
     */
    public function testInit()
    {
        $otherModel = new Model();
        $this->object->init();
        $this->assertEquals($this->object->validator, $otherModel->validator);
        $this->assertEquals($this->object->errors, $otherModel->errors);
        $this->assertEquals($this->object->datas, $otherModel->datas);
    }

    /**
     * @covers Itkg\Service\Model::getClassMap
     */
    public function testGetClassMap()
    {
        $this->assertEquals(
          get_class($this->object),
          $this->object->getClassMap()      
        );
    }

    /**
     * @covers Itkg\Service\Model::getErrors
     */
    public function testGetErrors()
    {
        $this->assertEquals(0, sizeof($this->object->getErrors()));
    }

    /**
     * @covers Itkg\Service\Model::injectDatas
     */
    public function testInjectDatas()
    {
        $this->object->injectDatas();
        $this->assertEquals(0, sizeof($this->object->datas));
        $this->assertInternalType('array', $this->object->datas);
        $this->object->injectDatas($this->datas);
        $this->assertEquals(2, sizeof($this->object->datas));
        $this->assertEquals($this->datas, $this->object->datas);
    }

    /**
     * @covers Itkg\Service\Model::__toXML
     */
    public function test__toXML()
    {
        $result = $this->object->__toXML();
        $this->assertNull($result);
    }

    /**
     * @covers Itkg\Service\Model::__toLog
     */
    public function test__toLog()
    {
        $result = $this->object->__toLog();
        $this->assertNull($result);
    }

    /**
     * @covers Itkg\Service\Model::__toRequest
     */
    public function test__toRequest()
    {
        $result = $this->object->__toRequest();
        $this->assertNull($result);
    }

    /**
     * @covers Itkg\Service\Model::__toArray
     */
    public function test__toArray()
    {
        $result = $this->object->__toArray();
        $this->assertInternalType('array', $result);
        $this->assertNull($result['datas']);
        $this->assertNull($result['errors']);
        $this->assertNull($result['validator']);
    }

    /**
     * @covers Itkg\Service\Model::__toDatas
     */
    public function test__toDatas()
    {
        $this->object->injectDatas($this->datas);
        $result = $this->object->__toDatas();
        $this->assertInternalType('array', $result);
        $this->assertEquals($this->datas, $result);
        $this->assertEquals(2, sizeof($result));
    }

    /**
     * @covers Itkg\Service\Model::__set
     */
    public function test__set()
    {
        $this->object->__set("datas", array("123","1"));
        $this->assertInternalType('array', $this->object->datas);
        $this->assertEquals(2, sizeof($this->object->datas));
        $this->object = new Model;
        $this->object->__set("validator", $this->validator);
        $this->assertEquals($this->object->validator, $this->validator);
    }

    /**
     * @covers Itkg\Service\Model::__get
     */
    public function test__get()
    {
        $this->object->setValidator($this->validator);
        $this->assertEquals($this->object->__get("validator"), $this->validator);
    }
    
    /**
     * @covers Itkg\Service\Model::__isset
     */
    public function test__isset()
    {
        $this->object->setValidator($this->validator);
        $this->assertTrue($this->object->__isset("validator"));
        $this->object = new Model;
        $this->assertFalse($this->object->__isset("validator"));
        $this->assertFalse($this->object->__isset("testvariabledoesnotexsite"));
    }
}
