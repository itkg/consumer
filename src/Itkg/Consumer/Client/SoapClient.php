<?php

namespace Itkg\Consumer\Client;

use Itkg\Consumer\Request;
use Itkg\Consumer\Response;

class SoapClient extends \SoapClient implements ClientInterface
{
    public function sendRequest(Request $request, Response $response)
    {
        // TODO: Implement send() method.
    }
}
