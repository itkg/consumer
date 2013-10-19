<?php

namespace Itkg\Consumer;

use Itkg\Consumer\AbstractModel;
use Itkg\Consumer\HydratorInterface;

class Response extends AbstractModel
{
    protected $body;
    protected $header;
    protected $mapping;

    public function __construct(HydratorInterface $hydrator = null, $mapping = array())
    {
        $this->hydrator = $hydrator;
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
        $this->hydrate($datas['body'], array('mapping' => $this->mapping));

        $this->validate();
    }

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
