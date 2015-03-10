<?php

namespace Itkg\Consumer\Client;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Service\Client;
use Symfony\Component\HttpFoundation\Request;
use Itkg\Consumer\Response;

/**
 * Class RestClient
 *
 * A rest client using guzzle http client
 *
 * @package Itkg\Consumer\Client
 */
class RestClient extends Client implements ClientInterface
{
    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;

        parent::__construct();
    }

    /**
     * Send request & hydrate response with client response data (content / headers)
     *
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
     * @param Request $request
     *
     * @return RequestInterface
     */
    protected function getClientRequest(Request $request)
    {
        $uri     = $request->getUri();
        $headers = $request->headers->all();
        $body    = (string) $request->getContent();

        switch ($request->getMethod()) {
            case 'POST':
                return $this->post($uri, $headers, $body);
            case 'PUT':
                return $this->put($uri, $headers, $body);
            case 'DELETE':
                return $this->delete($uri, $headers, $body);
            default:
                return $this->get($uri, $headers);
        }
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }
}
