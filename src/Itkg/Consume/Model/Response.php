<?php

namespace Itkg\Consume\Model;

use Itkg\Consume\AbstractModel;

class Response extends AbstractModel
{
    protected $body;
    protected $header;

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
}