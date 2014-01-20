<?php


namespace Itkg\Http;

use Itkg\Http\Client;

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
      $this->object = new \Itkg\Http\Client();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    /**
     * @covers Itkg\Http\Client::getIp
     */
    public function testGetIp()
    {
        $_SERVER["REMOTE_ADDR"] = "test";
        $this->assertEquals("test", \Itkg\Http\Client::getIp());
    }     
    /**
     * @covers Itkg\Http\Client::getReferer
     */
    public function testGetReferer()
    {
        $_SERVER["HTTP_HOST"] = "myHost";
        $_SERVER["REQUEST_URI"] = "/myuri";
        $this->assertEquals("http://myHost/myuri", \Itkg\Http\Client::getReferer());
    }   
}