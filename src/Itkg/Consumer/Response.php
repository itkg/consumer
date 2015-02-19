<?php

namespace Itkg\Consumer;

use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * Class Response
 * @package Itkg\Consumer
 */
class Response extends BaseResponse
{
    /**
     * @var mixed
     */
    protected $deserialized;

    /**
     * @return mixed
     */
    public function getDeserializedContent()
    {
        return $this->deserialized;
    }

    /**
     * @param $deserializedContent
     *
     * @return $this
     */
    public function setDeserializedContent($deserializedContent)
    {
        $this->deserialized = $deserializedContent;

        return $this;
    }
}
