<?php

namespace Itkg\Consumer\Client;

use Symfony\Component\HttpFoundation\Request;
use Itkg\Consumer\Response;

class RestClientTest extends \PHPUnit_Framework_TestCase
{
    public function testSendRequest()
    {
        $client = new RestClient();
        $response = new Response();
        $client->sendRequest(
            Request::create('http://www.google.fr'),
            $response
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertNotNull($response->getContent());
        $this->assertNotEmpty($response->headers->all());
    }

    public function testMethods()
    {
        $client = $this->getMock('Itkg\Consumer\Client\RestClient', array());

        $client->expects($this->at(0))->method('post');
        $client->expects($this->at(1))->method('put');
        $client->expects($this->at(2))->method('delete');

        $response = new Response();
        $request = Request::create('http://www.google.fr', 'POST');

        $client->sendRequest(
            $request,
            $response
        );

        $request = Request::create('http://www.google.fr', 'PUT');

        $client->sendRequest(
            $request,
            $response
        );

        $request = Request::create('http://www.google.fr', 'DELETE');

        $client->sendRequest(
            $request,
            $response
        );
    }

    public function testGetSetNormalizedOptions()
    {
        $client = new RestClient();

        $options = array(
            'auth_login'     => '',
            'auth_password'  => '',
            'proxy_login'    => '',
            'proxy_password' => '',
            'proxy_port'     => '',
            'proxy_host'     => '',
            'timeout'        => '',
            'base_url'       => ''
        );

        $this->assertEquals($options, $client->getNormalizedOptions());

        $options = array(
            'auth_login'     => 'login',
            'auth_password'  => 'pwd',
            'proxy_login'    => 'pxlogin',
            'proxy_password' => 'pxpwd',
            'proxy_port'     => '8080',
            'proxy_host'     => 'my_proxy',
            'timeout'        => 12,
            'base_url'       => 'http://service.test.com'
        );

        $client->setNormalizedOptions($options);

        $config = $client->getOptions();

        $this->assertEquals('http://service.test.com', $client->getBaseUrl());
        $this->assertEquals(12, $config['curl.options']['CURLOPT_TIMEOUT']);
        $this->assertEquals('pxlogin:pxpwd', $config['curl.options']['CURLOPT_PROXYUSERPWD']);
        $this->assertEquals('my_proxy:8080', $config['curl.options']['CURLOPT_PROXY']);
        $this->assertEquals('login', $config['request.options']['auth'][0]);
        $this->assertEquals('pwd', $config['request.options']['auth'][1]);

        $normalizedOptions = $client->getNormalizedOptions();
        $this->assertEquals($options, $normalizedOptions);
    }
}
