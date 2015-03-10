<?php

namespace Itkg\Consumer\Client;

use Symfony\Component\HttpFoundation\Request;
use Itkg\Consumer\Response;

/**
 * Interface ClientInterface
 *
 * @package Itkg\Consumer\Client
 */
interface ClientInterface
{
    /**
     * Send Request & hydrate Response with client response
     *
     * @param Request $request
     * @param Response $response
     */
    public function sendRequest(Request $request, Response $response);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options);

}
