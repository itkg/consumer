<?php

namespace Itkg\Consumer\Service;

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
}
