<?php


namespace Itkg\Ldap;

use Itkg\Ldap\Client;

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
      $this->object = new \Itkg\Ldap\Client();
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
        $optsToTest = array (
            'host' => null,
            'port' => 0,
            'useSsl' => false,
            'username' => null,
            'password' => null,
            'bindRequiresDn' => false,
            'baseDn' => null,
            'accountCanonicalForm' => null,
            'accountDomainName' => null,
            'accountDomainNameShort' => null,
            'accountFilterFormat' => null,
            'allowEmptyPassword' => false,
            'useStartTls' => false,
            'optReferrals' => false,
            'tryUsernameSplit' => true,
            'networkTimeout' => null
        );
        $opts = $this->object->getOptions();
        $this->assertEquals($opts, $optsToTest);
    }     
}