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

        parent::__construct('', $options);
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
        $uri     = $request->getRequestUri();

        if ('' === $this->getBaseUrl()) {
            $uri = $request->getUri();
        }
        // Remove host to allow baseUrl override
        $request->headers->remove('host');
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
    public function getNormalizedOptions()
    {
        $config = $this->getConfig();
        return array(
            'auth_login'     => '',
            'auth_password'  => '',
            'proxy_login'    => substr(
                $config['curl.options']['CURLOPT_PROXYUSERPWD'],
                0,
                strrpos($config['curl.options']['CURLOPT_PROXYUSERPWD'], ':') - 1
            ),
            'proxy_password' => substr(
                $config['curl.options']['CURLOPT_PROXYUSERPWD'],
                strrpos($config['curl.options']['CURLOPT_PROXYUSERPWD'], ':')
            ),
            'proxy_port'     => substr(
                $config['curl.options']['CURLOPT_PROXY'],
                strrpos($config['curl.options']['CURLOPT_PROXY'], ':')
            ),
            'proxy_host'     => substr(
                $config['curl.options']['CURLOPT_PROXY'],
                0,
                strrpos($config['curl.options']['CURLOPT_PROXY'], ':') - 1
            ),
            'timeout'        => $config['curl.options']['CURLOPT_TIMEOUT'],
            'base_url'       => ''
        );
    }

    /**
     * @param array $normalizedOptions
     *
     * @return $this
     */
    public function setNormalizedOptions(array $normalizedOptions)
    {
        if (isset($normalizedOptions['base_url'])) {
            $this->setBaseUrl($normalizedOptions['base_url']);
        }

        if (!empty($normalizedOptions['timeout'])) {
            $this->options['curl.options']['CURLOPT_TIMEOUT'] = $normalizedOptions['timeout'];
        }

        if (!empty($normalizedOptions['proxy_host'])) {
            $this->options['curl.options']['CURLOPT_PROXY'] = $normalizedOptions['proxy_host'].':'.$normalizedOptions['proxy_port'];
        }

        if (!empty($normalizedOptions['proxy_login'])) {
            $this->options['curl.options']['CURLOPT_PROXYUSERPWD'] = $normalizedOptions['proxy_login'].':'.$normalizedOptions['proxy_password'];
        }

        $this->setConfig($this->options);
        return $this;
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
