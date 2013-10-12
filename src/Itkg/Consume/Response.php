<?php

namespace Itkg\Consume;

use Itkg\Consume\AbstractModel;

class Response extends AbstractModel
{
    protected $body;
    protected $header;
    protected $format;
    protected $mapping;

    public function __construct($format, array $mapping = array())
    {
        $this->format = $format;
        $this->mapping = $mapping;
    }

    public function bind($datas = array())
    {
        if(isset($datas['body'])) {
            $this->body = $datas['body'];
        }
        if(isset($datas['header'])) {
            $this->header = $datas['header'];
        }

        $this->setDatas($datas);
        $this->format();
        $this->validate();
    }

    public function format() {}

    public function getBody()
    {
        return $this->body;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function setHeader($header)
    {
        $this->header = $header;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function getMapping()
    {
        return $this->mapping;
    }

    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
    }
}
