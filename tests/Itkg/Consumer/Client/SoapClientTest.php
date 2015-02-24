<?php

namespace Itkg\Consumer\Client;

use Itkg\Consumer\Service\Service;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class SoapClientTest extends \PHPUnit_Framework_TestCase
{
    public function testSendRequest()
    {
        $client = $this->getMockBuilder('Itkg\Consumer\Client\SoapClient')
            ->disableOriginalConstructor()
            ->setMethods(array('__soapCall'))
            ->getMock();
        $client->expects($this->once())->method('__soapCall')->will($this->returnValue('My SOAP Response'));

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())->method('getContent');


        $service = new Service(new EventDispatcher(), $client, array('identifier' => 'identifier'));
        $response = $service->sendRequest($request)->getResponse();

        $this->assertEquals('My SOAP Response', $response->getContent());
    }

    public function testSecurity()
    {
        $optionsTotest = array(
            'connection_timeout' => 2,
            'trace'              => false,
            'encoding'           => 'UTF8',
            'soap_version'       => SOAP_1_1,
            'features'           => SOAP_SINGLE_ELEMENT_ARRAYS,
            'namespace'          => 'http://my_namespace.com',
            'uri'       => '/my_ws',
            'location'  => 'http://location'
        );
        $header = new \SoapHeader(
            'http://my_namespace.com',
            'Security',
            new \SoapVar(
                    '<wsse:Security  xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <wsse:UsernameToken wsu:Id="UsernameToken-6868426" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
            <wsse:Username>my_login</wsse:Username>
            <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">xxxx</wsse:Password>
        </wsse:UsernameToken>
    </wsse:Security>',
                XSD_ANYXML,
                NULL,
                'http://my_namespace.com'
        ));
        $options = array(
            'login'     => 'my_login',
            'password'  => 'xxxx',
            'namespace' => 'http://my_namespace.com',
            'uri'       => '/my_ws',
            'location'  => 'http://location'
        );
        $client = $this->getMock('Itkg\Consumer\Client\SoapClient', array('__soapCall'), array(NULL, $options));
        $client->expects($this->once())->method('__soapCall')->with('/', array(new \SoapVar('', XSD_ANYXML)), $optionsTotest, $header);
        $service = new Service(new EventDispatcher(), $client, array('identifier' => 'identifier'));
        $service->sendRequest(Request::createFromGlobals())->getResponse();
    }
}
