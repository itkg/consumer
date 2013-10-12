<?php

namespace Itkg\Consume\Service;

use Itkg\Config;
use Itkg\Consume\ClientInterface;
use Itkg\Consume\Request;
use Itkg\Consume\Response;

class Method extends Config
{
    protected $loggers;
    protected $identifier;
    protected $request;
    protected $response;
    protected $client;

    public function __construct($identifier, Request $request, Response $response, ClientInterface $client)
    {
        $this->identifier = $identifier;
        $this->request = $request;
        $this->response = $response;
        $this->client = $client;
    }

    public function validate()
    {
        // Validate method config & params ?
    }

    public function call()
    {
        $this->client->init($this->request);
        $this->client->call();
        $this->response->bind($this->client->getResponse());

        return $this->response;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
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

    public function getLoggers()
    {
        return $this->loggers;
    }

    public function setLoggers(array $loggers)
    {
        $this->loggers = $loggers;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }
}