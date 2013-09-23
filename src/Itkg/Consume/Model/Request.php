<?php

namespace Itkg\Consume\Model;

use Itkg\Consume\AbstractModel;

class Request extends AbstractModel
{
    protected $uri;
    protected $host;
    protected $headers;
    protected $method;

    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    public function create()
    {
        return ''; // Return an emtpy request
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
}