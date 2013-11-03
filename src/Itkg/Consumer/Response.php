<?php

namespace Itkg\Consumer;

use Itkg\Consumer\AbstractModel;
use Itkg\Consumer\HydratorInterface;

/**
 * Class Response
 *
 * @package Itkg\Consumer
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class Response extends AbstractModel
{
    protected $body;
    protected $header;
    protected $options;

    public function __construct(HydratorInterface $hydrator = null, $options = array())
    {
        $this->hydrator = $hydrator;
        $this->options = $options;
    }

    public function bind($data = array())
    {
        if(isset($data['body'])) {
            $this->body = $data['body'];
        }
        if(isset($data['header'])) {
            $this->header = $data['header'];
        }

        $this->setData($data);
        $this->hydrate($data['body'], $this->options);

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

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }
}
