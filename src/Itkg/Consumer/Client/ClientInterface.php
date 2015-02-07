<?php

namespace Itkg\Consumer\Client;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface ClientInterface
 *
 * @package Itkg\Consumer\Client
 */
interface ClientInterface
{
    public function sendRequest(Request $request, Response $response);
}
