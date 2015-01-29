<?php

namespace Itkg\Consumer\Client;

use Itkg\Consumer\Request;
use Itkg\Consumer\Response;

interface ClientInterface
{
    public function sendRequest(Request $request, Response $response);
}
