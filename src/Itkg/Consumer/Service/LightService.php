<?php

namespace Itkg\Consumer\Service;

use Itkg\Consumer\Client\ClientInterface;
use Itkg\Consumer\Event\ServiceEvent;
use Itkg\Consumer\Event\ServiceEvents;
use Symfony\Component\HttpFoundation\Request;
use Itkg\Core\ConfigInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;

class LightService
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;
    /**
     * @var ClientInterface
     */
    protected $client;
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher
     * @param ConfigInterface $config
     * @param ClientInterface $client
     * @param Request $request
     * @param Response $response
     */
    public function __construct(EventDispatcher $eventDispatcher, ConfigInterface $config, ClientInterface $client, Request $request = null, Response $response = null)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->config          = $config;
        $this->request         = $request;
        $this->response        = $response;
        $this->client          = $client;
    }

    /**
     * Send request using current client
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function sendRequest()
    {
        $event = new ServiceEvent($this);
        $this->eventDispatcher->dispatch(ServiceEvents::REQUEST, $event);

        if (null != $this->response->getContent()) {
            return $this;
        }

        try {
            $this->client->sendRequest($this->request, $this->response);

            $this->eventDispatcher->dispatch(ServiceEvents::RESPONSE, $event);

        } catch(\Exception $e) {
            $this->exception = $e;
            $this->eventDispatcher->dispatch(ServiceEvents::EXCEPTION, $event);

            throw $e;
        }
        return $this;
    }

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get client bases on config settings
     * restClient is default client
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param ClientInterface $client
     *
     * @return $this
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->config->get('identifier');
    }
}
