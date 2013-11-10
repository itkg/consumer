<?php

namespace Itkg\Consumer;

use Itkg\Consumer\AbstractModel;
use Itkg\Consumer\Hydrator\Simple;
use Itkg\Consumer\HydratorInterface;

/**
 * Class Request
 * @package Itkg\Consumer
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
abstract class Request extends AbstractModel
{
    /**
     * Request uri
     *
     * @var string
     */
    protected $uri;
    /**
     * Request host
     *
     * @var string
     */
    protected $host;
    /**
     * List of headers
     *
     * @var array
     */
    protected $headers;
    /**
     * HTTP method (get, put, post, delete)
     *
     * @var string
     */
    protected $method;
    /**
     * @var
     */
    protected $scope;

    /**
     * Constructor
     *
     * @param string $host Request host
     * @param string $uri Request uri
     * @param string $method HTTP method
     * @param array $headers List of headers
     * @param HydratorInterface $hydrator Object hydrator
     */
    public function __construct(
        $host,
        $uri = '',
        $method = 'GET',
        $headers = array(),
        HydratorInterface $hydrator = null
    ) {
        $this->host = $host;
        $this->uri = $uri;
        $this->method = $method;
        $this->headers = $headers;
    }

    /**
     * Update scope
     * - If scope == SESSION, session_id() is passed into request's cookie
     */
    public function updateScope()
    {
        if ($this->scope == 'SESSION') {
            $this->headers['cookies']['PHPSESSID'] = session_id();
        }
    }

    /**
     * Getter uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Setter uri
     *
     * @param $uri Request uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Getter host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Setter host
     *
     * @param string $host Request host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Getter hydrator
     *
     * @return Simple|HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->hydrator = new Simple();
        }
        return $this->hydrator;
    }

    /**
     * Getter headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Setter headers
     *
     * @param $headers List of headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Getter method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Setter method
     *
     * @param string $method HTTP method
     */
    public function setMethod($method = 'GET')
    {
        $this->method = $method;
    }

    /**
     * Getter scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Setter scope
     *
     * @param string $scope Request scope (SESSION, PAGE)
     */
    public function setScope($scope = 'PAGE')
    {
        $this->scope = $scope;
        $this->updateScope();
    }

    /**
     * Getter identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return md5(serialize($this));
    }

    /**
     * Create method. Return request values
     *
     * @return mixed
     */
    abstract public function create();
}