<?php

namespace Itkg\Consume\Service;

use Itkg\Config;
use Itkg\Consume\Model\Request;
use Itkg\Consume\Model\Response;

class Method extends Config
{
    protected $logger;
    protected $cacheClass;
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

    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger(AbstractLogger $logger)
    {
        $this->logger = $logger;
    }

    public function getCacheClass()
    {
        return $this->cacheClass;
    }

    public function setCacheClass($cacheClass)
    {
        $this->cacheClass = $cacheClass;
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