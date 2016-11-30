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
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Constructor.
     *
     * @param mixed $content The response content, see setContent()
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     *
     * @throws \InvalidArgumentException When the HTTP status code is not valid
     */
    public function __construct($content = '', $status = 200, $headers = array())
    {
        parent::__construct($content, $status, $headers);
        $this->createdAt = new \DateTime();
    }

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

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
