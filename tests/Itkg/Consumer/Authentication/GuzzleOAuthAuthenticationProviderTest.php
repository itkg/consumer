<?php

namespace Itkg\Consumer\Authentication;

use fkooman\Guzzle\Plugin\BearerAuth\BearerAuth;
use Itkg\Consumer\Service\Service;
use Symfony\Component\EventDispatcher\EventDispatcher;


/**
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class GuzzleOAuthAuthenticationProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidClient()
    {
        $client = $this->getMockBuilder('Itkg\Consumer\Client\SoapClient')
            ->disableOriginalConstructor()
            ->getMock();

        $provider = $this->getMock(
            'Itkg\Consumer\Authentication\GuzzleOAuthAuthenticationProvider',
            array('getToken'),
            array(),
            '',
            false
        );

        $service = new Service(new EventDispatcher(), $client, array('identifier' => 'auth'));
        $provider->hydrate($service);
    }
}
