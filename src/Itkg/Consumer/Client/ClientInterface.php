<?php

namespace Itkg\Consumer\Client;

use Itkg\Consumer\Request;
use Symfony\Component\HttpFoundation\Response;

interface ClientInterface
{
    public function sendRequest(Request $request, Response $response);
}
