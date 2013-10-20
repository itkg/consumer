<?php

namespace Itkg\Consumer;

use Itkg\Consumer\AbstractModel;
use Itkg\Consumer\Hydrator\Simple;
use Lemon\Hydrator\HydratorInterface;

abstract class Request extends AbstractModel
{
    protected $uri;
    protected $host;
    protected $headers;
    protected $method;
    protected $scope;

    public function __construct($host, $uri = '', $method = 'GET', $headers = array(), HydratorInterface $hydrator = null)
    {
        $this->host = $host;
        $this->uri = $uri;
        $this->method = $method;
        $this->headers = $headers;
    }

    public function updateSCope()
    {
        if($this->scope == 'SESSION') {
            $this->headers['cookies']['PHPSESSID'] = session_id();
        }
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function getHydrator()
    {
        if(!$this->hydrator) {
            $this->hydrator = new Simple();
        }
        return $this->hydrator;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setHeaders($headers)
    {
        return $this->headers = $headers;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method = 'GET')
    {
        $this->method = $method;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function setScope($scope = 'PAGE')
    {
        $this->scope = $scope;
        $this->updateScope();
    }

    public function getIdentifier()
    {
        return md5(serialize($this));
    }

    abstract public function create();
}