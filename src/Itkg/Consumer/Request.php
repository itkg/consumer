<?php

namespace Itkg\Consumer;

class Request
{
    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var string
     */
    protected $body;

    /**
     * @param null|string $body
     * @param array $headers
     */
    public function __construct($body = null, $headers = array())
    {
        $this->body    = $body;
        $this->headers = $headers;
    }

    /**
     * @return null|string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers = array())
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Add a couple of key / value header
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Get request identifier
     *
     * @return string
     */
    public function getHash()
    {
        return md5(sprintf('%s_%s', implode($this->headers), $this->body));
    }
}
