<?php

namespace Itkg\Consume;

use Itkg\Consume\AbstractModel;

abstract class Request extends AbstractModel
{
    protected $uri;
    protected $host;
    protected $headers;
    protected $method;

    public function __construct($host, $uri = '', $method = 'GET', $headers = array())
    {
        $this->host = $host;
        $this->uri = $uri;
        $this->method = $method;
        $this->headers = $headers;
    }

    public function getParams()
    {
        return $this->params;
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

    public function getIdentifier()
    {
        return md5(serialize($this));
    }

    abstract public function create();
}