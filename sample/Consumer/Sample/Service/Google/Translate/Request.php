<?php

namespace Consumer\Sample\Service\Google\Translate;

use Itkg\Consumer\Request as BaseRequest;

class Request extends BaseRequest
{
    protected $apiKey;
    protected $source;
    protected $target;
    protected $content;

    public function __construct($host, $uri = '', $method = 'GET', $headers = array(), $apiKey = '')
    {
        $this->apiKey = $apiKey;
        parent::__construct($host, $uri, $method, $headers);
    }

    public function create()
    {
        return array(
            'key'    => $this->apiKey,
            'source' => $this->source,
            'target' => $this->target,
            'q'      => $this->content
        );
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function setTarget($target)
    {
        $this->target = $target;
    }
}