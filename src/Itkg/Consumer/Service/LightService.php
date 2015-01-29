<?php

namespace Itkg\Consumer\Service;

use Itkg\Consumer\Client\ClientInterface;
use Itkg\Consumer\Event\ServiceEvent;
use Itkg\Consumer\Event\ServiceEvents;
use Itkg\Consumer\Request;
use Itkg\Consumer\Response;
use Itkg\Core\ConfigInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LightService
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Response
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

        if (null !== $this->response->getContent()) {
            return $this;
        }

        try {
            $this->client->sendRequest($this->request, $this->response);

            $this->eventDispatcher->dispatch(ServiceEvents::RESPONSE, $event);

        } catch(\Exception $e) {
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
     * @param \Itkg\Consumer\Request $request
     *
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return \Itkg\Consumer\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Itkg\Consumer\Response $response
     *
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return \Itkg\Consumer\Response
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

    public function setClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }
}
