<?php

namespace Itkg\Consumer;

use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * Class Response
 *
 * Base on Symfony 2 Response, This response add deserialized content property
 * For WS normalisation
 *
 * @package Itkg\Consumer
 */
class Response extends BaseResponse
{
    /**
     * @var mixed
     */
    protected $deserialized;

    /**
     * Get deserializedContent
     * 
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
