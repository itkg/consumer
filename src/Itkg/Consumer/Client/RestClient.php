<?php

namespace Itkg\Consumer\Client;

use Guzzle\Service\Client;
use Itkg\Consumer\Request;
use Itkg\Consumer\Response;
use Itkg\Core\ConfigInterface;

class RestClient extends Client implements ClientInterface
{

    public function sendRequest(Request $request, \Symfony\Component\HttpFoundation\Response $response)
    {
        // TODO: Implement sendRequest() method.
    }
}
