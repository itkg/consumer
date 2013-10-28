<?php

namespace Itkg\Consumer\Authentication\Provider;

use \Itkg\Consumer\ClientInterface;
use Itkg\Consumer\Authentication\Provider;
use Itkg\Consumer\Request;
use Itkg\Consumer\Response;

/**
 * Class OAuth
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class OAuth implements ProviderInterface
{
    protected $key;
    protected $secret;
    protected $client;
    protected $request;
    protected $response;

    public function __construct(ClientInterface $client, Request $request, Response $response, $key, $secret)
    {
        $this->client = $client;
        $this->request = $request;
        $this->response = $response;
        $this->key = $key;
        $this->secret = $secret;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    public function setSecret($secret)
    {
        $this->secret = $secret;
    }
}