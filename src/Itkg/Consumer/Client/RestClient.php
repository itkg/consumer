<?php

namespace Itkg\Consumer\Client;

use Guzzle\Service\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RestClient extends Client implements ClientInterface
{
    /**
     * @var array
     */
    private $options = array();

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;

        parent::__construct();
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function sendRequest(Request $request, Response $response)
    {
        $clientResponse = $this->getClientRequest($request)->send();

        $response
            ->setContent($clientResponse->getBody(true))
            ->headers->add($clientResponse->getHeaders()->getAll());
    }

    /**
     * Get a guzzle request object for a Request
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Guzzle\Http\Message\RequestInterface
     */
    protected function getClientRequest(Request $request)
    {
        $uri     = $request->getUri();
        $headers = $request->headers->all();
        $body    = $request->getContent();

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
