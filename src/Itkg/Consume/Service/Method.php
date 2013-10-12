<?php

namespace Itkg\Consume\Service;

use Itkg\Config;
use Itkg\Consume\Model\Request;
use Itkg\Consume\Model\Response;

class Method extends Config
{
    protected $loggers;
    protected $identifier;
    protected $request;
    protected $response;
    protected $protocol;

    public function validate()
    {
        // Validate method config & params ?
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

    public function getProtocol()
    {
        return $this->protocol;
    }

    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    }
}