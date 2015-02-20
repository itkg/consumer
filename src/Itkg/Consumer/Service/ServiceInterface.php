<?php

namespace Itkg\Consumer\Service;

use Itkg\Consumer\Client\ClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Itkg\Consumer\Response;

interface ServiceInterface
{
    /**
     * Send a request
     *
     * @param Request $request
     * @param Response $response
     *
     * @return $this
     */
    public function sendRequest(Request $request, Response $response = null);

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest($request);

    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @param Response $response
     *
     * @return $this
     */
    public function setResponse($response);

    /**
     * @return Response
     */
    public function getResponse();

    /**
     * Get client bases on config settings
     * restClient is default client
     *
     * @return ClientInterface
     */
    public function getClient();

    /**
     * @param ClientInterface $client
     *
     * @return $this
     */
    public function setClient(ClientInterface $client);

    /**
     * @return \Exception
     */
    public function getException();
}
