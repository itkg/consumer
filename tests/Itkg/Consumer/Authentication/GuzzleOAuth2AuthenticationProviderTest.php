<?php

namespace Itkg\Consumer\Authentication;

use fkooman\Guzzle\Plugin\BearerAuth\BearerAuth;
use Itkg\Consumer\Service\Service;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class GuzzleOAuth2AuthenticationProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testHydrate()
    {
        $client = $this->getMockBuilder('Itkg\Consumer\Client\RestClient')
            ->disableOriginalConstructor()
            ->getMock();

        $client->expects($this->once())->method('addSubscriber')->with(new BearerAuth('MY_BEARER_TOKEN'));

        $provider = $this->getMock(
            'Itkg\Consumer\Authentication\GuzzleOAuth2AuthenticationProvider',
            array('getToken'),
            array(),
            '',
            false
         );

        $provider->expects($this->once())->method('getToken')->will($this->returnValue('MY_BEARER_TOKEN'));

        $service = new Service(new EventDispatcher(), $client, array('identifier' => 'auth'));
        $provider->hydrate($service);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidClient()
    {
        $client = $this->getMockBuilder('Itkg\Consumer\Client\SoapClient')
            ->disableOriginalConstructor()
            ->getMock();

        $provider = $this->getMock(
            'Itkg\Consumer\Authentication\GuzzleOAuth2AuthenticationProvider',
            array('getToken'),
            array(),
            '',
            false
        );

        $service = new Service(new EventDispatcher(), $client, array('identifier' => 'auth'));
        $provider->hydrate($service);
    }
}
