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
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        parent::__construct('', $config);
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
        $proxyUserPwd = $proxy = $timeout = '';

        if (isset($config['curl.options']['CURLOPT_PROXYUSERPWD'])) {
            $proxyUserPwd = $config['curl.options']['CURLOPT_PROXYUSERPWD'];
        }

        if (isset($config['curl.options']['CURLOPT_PROXY'])) {
            $proxy = $config['curl.options']['CURLOPT_PROXY'];
        }

        if (isset($config['curl.options']['CURLOPT_TIMEOUT'])) {
            $timeout = $config['curl.options']['CURLOPT_TIMEOUT'];
        }

        return array(
            'auth_login'     => $config['request.options']['auth'][0],
            'auth_password'  => $config['request.options']['auth'][1],
            'proxy_login'    => substr($proxyUserPwd, 0, strrpos($proxyUserPwd, ':')),
            'proxy_password' => substr($proxyUserPwd, strrpos($proxyUserPwd, ':') + 1),
            'proxy_port'     => substr($proxy, strrpos($proxy, ':') + 1),
            'proxy_host'     => substr($proxy, 0, strrpos($proxy, ':')),
            'timeout'        => $timeout,
            'base_url'       => $this->getBaseUrl()
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

        $config = array();
        if (!empty($normalizedOptions['timeout'])) {
            $config['curl.options']['CURLOPT_TIMEOUT'] = $normalizedOptions['timeout'];
        }

        if (!empty($normalizedOptions['proxy_host'])) {
            $config['curl.options']['CURLOPT_PROXY'] = $normalizedOptions['proxy_host'].':'.$normalizedOptions['proxy_port'];
        }

        if (!empty($normalizedOptions['proxy_login'])) {
            $config['curl.options']['CURLOPT_PROXYUSERPWD'] = $normalizedOptions['proxy_login'].':'.$normalizedOptions['proxy_password'];
        }

        if (!empty($normalizedOptions['auth_login'])) {
            $config['request.options']['auth'] = array($normalizedOptions['auth_login'], $normalizedOptions['auth_password']);
        }

        $this->setConfig($config);

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->getConfig()->getAll();
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->setConfig($options);

        return $this;
    }
}
