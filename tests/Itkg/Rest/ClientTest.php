<?php


namespace Itkg\Rest;

use Itkg\Rest\Client;

/**
 * Class Client
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class ClientTest extends \PHPUnit_Framework_TestCase {

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
      $this->object = new \Itkg\Rest\Client("http://testHost.com");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    /**
     * @covers Itkg\Rest\Client::__getLastRequest
     */
    public function test__getLastRequest()
    {
        $this->assertNull($this->object->__getLastRequest());
    }
    /**
     * @covers Itkg\Rest\Client::__getLastResponse
     */
    public function test__getLastResponse()
    {
        $this->assertNull($this->object->__getLastResponse());
    }
    /**
     * @covers Itkg\Rest\Client::makeUrl
     */
    public function testMakeUrl()
    {
        $data = array("test1"=>"testvalue","test2"=>"testvalue2");
        $this->assertEquals("http://test.com?test1=testvalue&test2=testvalue2", $this->object->makeUrl("http://test.com", $data));
    }    
    /**
     * @covers Itkg\Rest\Client::addOptions
     */
    public function testAddoptions()
    {
        $myopt = array("test");
        $this->object->addOptions($myopt);
        $attr = \PHPUnit_Framework_Assert::readAttribute($this->object, 'options');
        $this->assertEquals($myopt, $attr);
    }      
}