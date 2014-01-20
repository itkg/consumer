<?php


namespace Itkg\Xml;

use Itkg\Xml\ExtendedSimpleXMLElement;

/**
 * Class Client
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class ExtendedSimpleXMLElementTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \Itkg\Xml\ExtendedSimpleXMLElement
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {         
      $xml = "<xml><test>testIncludeVal</test></xml>";
      $this->object = new \Itkg\Xml\ExtendedSimpleXMLElement($xml);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    /**
     * @covers Itkg\Xml\ExtendedSimpleXMLElement::addCData
     */
    public function testAddCData()
    {
        $this->object->addCData("testCdata");
        $this->assertEquals("testCdata", $this->object->__toString());
    }     
}