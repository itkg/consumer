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
}
