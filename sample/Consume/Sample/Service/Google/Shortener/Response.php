<?php

namespace Consume\Sample\Service\Google\Shortener;

use Itkg\Consume\Response as BaseResponse;

class Response extends BaseResponse
{
    protected $kind;
    protected $id;
    protected $longUrl;

    public function getId()
    {
        return $this->id;
    }

    public function getKind()
    {
        return $this->king;
    }

    public function getLongUrl()
    {
        return $this->longUrl;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setLongUrl($longUrl)
    {
        $this->longUrl = $longUrl;
    }

    public function setKind($kind)
    {
        $this->kind = $kind;
    }
}