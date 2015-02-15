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
    protected $decodedContent;

    /**
     * @return mixed
     */
    public function getDecodedContent()
    {
        return $this->decodedContent;
    }

    /**
     * @param $decodedContent
     * @return $this
     */
    public function setDecodedContent($decodedContent)
    {
        $this->decodedContent = $decodedContent;

        return $this;
    }
}
