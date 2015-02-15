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
}
