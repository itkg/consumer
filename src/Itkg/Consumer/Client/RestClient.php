<?php

namespace Itkg\Consumer\Client;

use Guzzle\Service\Client;
use Itkg\Consumer\Request;
use Itkg\Consumer\Response;

class RestClient extends Client implements ClientInterface
{

    public function sendRequest(Request $request, \Symfony\Component\HttpFoundation\Response $response)
    {
        $request = null;
        $this->addOptions($options);
        $headers = null;
        if (isset($this->options['headers'])) {
            $headers = $this->options['headers'];
        }
        $request = $this->getClientRequest($request);
        if ($request) {
            // Si login et password, on procède à l'authentification
            $request = $this->hydrateRequest($request);
            // Envoi de la requete
            $response = $request->send();
            $aResponseDatas = array();
            // Récupération du header
            $aResponseDatas['headers'] = $response->getMessage();
            // Récupération du body
            $aResponseDatas['body'] = $response->getBody(true);
            return $aResponseDatas;
    }

    /**
     * Get a guzzle request object for a Request
     *
     * @return \Guzzle\Http\Message\RequestInterface
     */
    protected function getClientRequest(Request $request)
    {
        $uri = $request->getUri();
        $headers = $request->headers->all();
        $body = $request->getContent();

        switch ($request->getMethod()) {
            case 'POST':
                return $this->post($uri, $headers, $body);
                break;
            case 'PUT':
                return $this->put($uri, $headers, $body);
                break;
            case 'DELETE':
                return $this->delete($uri, $headers, $body);
                break;
            default:
                return $this->get($uri, $headers);
                break;
        }
    }
}
