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
            ->setMethods(array('__soapCall', '__getLastResponse'))
            ->getMock();

        $client->expects($this->once())
            ->method('__soapCall')
            ->will($this->returnValue('My SOAP Response'));

        $client->expects($this->once())
            ->method('__getLastResponse')
            ->will($this->returnValue('My SOAP Response'));

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue('My SOAP Response'));

        $service = new Service(new EventDispatcher(), $client, array('identifier' => 'identifier'));
        $response = $service->sendRequest($request)->getResponse();

    }

    public function testHeaderWithSecurity()
    {
        $optionsTotest = array(
            'connection_timeout' => 2,
            'trace' => false,
            'encoding' => 'UTF8',
            'soap_version' => SOAP_1_1,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'namespace' => 'http://my_namespace.com',
            'uri' => '/my_ws',
            'location' => 'http://location',
            'must_understand' => true,
            'signature' => 'my_signature',
            'signature_namespace' => 'http://host/for/my/signature',
            'login' => 'my_auth_login',
            'password' => 'my_auth_password'
        );
        $headerXML = <<<EOF
<wsse:Security SOAP-ENV:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <wsse:UsernameToken wsu:Id="UsernameToken-6868426" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
            <wsse:Username>my_login</wsse:Username>
            <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">xxxx</wsse:Password>
        </wsse:UsernameToken>
    </wsse:Security><Signature xmlns="http://host/for/my/signature">my_signature</Signature>
EOF;

        $header = new \SoapHeader(
            'http://my_namespace.com',
            'Security',
            new \SoapVar(
                $headerXML,
                XSD_ANYXML,
                null,
                'http://my_namespace.com'
            )
        );
        $options = array(
            'login' => 'my_login',
            'password' => 'xxxx',
            'namespace' => 'http://my_namespace.com',
            'uri' => '/my_ws',
            'location' => 'http://location',
            'must_understand' => true,
            'signature' => 'my_signature',
            'signature_namespace' => 'http://host/for/my/signature',
            'http_auth_login' => 'my_auth_login',
            'http_auth_password' => 'my_auth_password'
        );
        $client = $this->getMock(
            'Itkg\Consumer\Client\SoapClient',
            array('__soapCall', '__getLastResponse'),
            array(null, $options)
        );
        $client->expects($this->once())
            ->method('__getLastResponse')
            ->will($this->returnValue('My SOAP Response'));

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->setMethods(array('getContent', 'getPathInfo'))
            ->getMock();

        $request->expects($this->once())
            ->method('getContent')
            ->willReturn('My SOAP Response');

        $request->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/'));

        $client->expects($this->once())
            ->method('__soapCall')
            ->with(
                '/',
                array(new \SoapVar('My SOAP Response', XSD_ANYXML)),
                $optionsTotest,
                $header
        );

        $service = new Service(new EventDispatcher(), $client, array('identifier' => 'identifier'));
        $service->sendRequest($request)->getResponse();
    }
}
