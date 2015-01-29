<?php

namespace Itkg\Consumer\Client;

use Guzzle\Service\Client;
use Itkg\Consumer\Request;
use Itkg\Consumer\Response;

class RestClient extends Client implements ClientInterface
{

    public function sendRequest(Request $request, Response $response)
    {
        // TODO: Implement sendRequest() method.
    }
}
