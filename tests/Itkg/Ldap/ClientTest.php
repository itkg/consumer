<?php


namespace Itkg\Ldap;

use Itkg\Mock\Ldap\Client;

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
      $this->object = new \Itkg\Mock\Ldap\Client();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    /**
     * @covers Itkg\Ldap\Client::getOptions
     */
    public function testGetoptions()
    {
        $opts = $this->object->getOptions();
        $this->assertNull($opts);
    }     
}