<?php

namespace Itkg\Consumer\Service;

use Itkg\Consumer\Client\ClientInterface;
use Itkg\Consumer\Response;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractService implements ServiceInterface
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Response
     */
    protected $response;
    /**
     * @var ClientInterface
     */
    protected $client;
    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Response $response
     *
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get client bases on config settings
     * restClient is default client
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param ClientInterface $client
     *
     * @return $this
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
