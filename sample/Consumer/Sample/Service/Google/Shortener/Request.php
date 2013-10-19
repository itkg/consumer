<?php

namespace Consumer\Sample\Service\Google\Shortener;

use Itkg\Consumer\Request as BaseRequest;

class Request extends BaseRequest
{
    protected $apiKey;
    protected $url;

    public function __construct($host, $uri = '', $method = 'GET', $headers = array(), $apiKey = '')
    {
        $this->apiKey = $apiKey;
        parent::__construct($host, $uri, $method, $headers);
    }

    public function create()
    {
        return json_encode(array(
            'longUrl' => $this->url
        ));
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function toLog()
    {
        return 'URL : '.$this->url;
    }
}