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
    /**
     * Response body
     *
     * @var string
     */
    protected $body;
    /**
     * Response header
     *
     * @var string
     */
    protected $header;
    /**
     * List of options
     *
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param HydratorInterface $hydrator An object hydrator
     * @param array $options List of options
     */
    public function __construct(HydratorInterface $hydrator = null, $options = array())
    {
        $this->hydrator = $hydrator;
        $this->options = $options;
    }

    /**
     * Bind data into response object
     *
     * @param array $data List of params
     */
    public function bind($data = array())
    {
        // Separate body & header
        if (isset($data['body'])) {
            $this->body = $data['body'];
        }
        if (isset($data['header'])) {
            $this->header = $data['header'];
        }

        $this->setData($data);
        // Hydrate body into me
        $this->hydrate($data['body'], $this->options);
        // Validate object
        $this->validate();
    }

    /**
     * Getter body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Getter header
     *
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Setter body
     *
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Setter header
     *
     * @param string $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * Getter options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Setter options
     *
     * @param array $options List of options
     */
    public function setOptions(array $options = array())
    {
        $this->options = $options;
    }
}
