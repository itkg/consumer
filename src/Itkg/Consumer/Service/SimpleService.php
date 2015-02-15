<?php

namespace Itkg\Consumer\Service;

use Itkg\Consumer\Client\ClientInterface;
use Itkg\Consumer\Event\ConfigEvent;
use Itkg\Consumer\Event\ServiceEvent;
use Itkg\Consumer\Event\ServiceEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SimpleService
 *
 * A minimal service for sending requests & handle responses
 *
 * @package Itkg\Consumer\Service
 */
class SimpleService implements ServiceInterface
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
     * @var array
     */
    protected $options;

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
     * @param ClientInterface $client
     * @param array|\Itkg\Core\ConfigInterface $options
     */
    public function __construct(EventDispatcher $eventDispatcher, ClientInterface $client, array $options = array())
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->client          = $client;

        $this->configure($options);
    }

    /**
     * Send request using current client
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function sendRequest(Request $request, Response $response = null)
    {
        $this->request = $request;
        $this->response = (null === $response) ? new Response() : $response;

        $event = new ServiceEvent($this);
        $this->eventDispatcher->dispatch(ServiceEvents::REQUEST, $event);

        if (null != $this->response->getContent()) {
            return $this;
        }

        try {
            $this->client->sendRequest($this->request, $this->response);

            $this->eventDispatcher->dispatch(ServiceEvents::RESPONSE, $event);

        } catch (\Exception $e) {
            $this->exception = $e;
            $this->eventDispatcher->dispatch(ServiceEvents::EXCEPTION, $event);

            throw $e;
        }
        return $this;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set all options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->configure($options);

        return $this;
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
        return $this->options['identifier'];
    }

    /**
     * Manage configuration
     *
     * @param array $options
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    protected function configure(array $options = array(), OptionsResolver $resolver = null)
    {
        if (null === $resolver) {
            $resolver = new OptionsResolver();
        }

        $this->eventDispatcher->dispatch(ServiceEvents::PRE_CONFIGURE, new ConfigEvent($resolver, $options));

        $resolver->setRequired('identifier');
        $this->options = $resolver->resolve($options);

        $this->eventDispatcher->dispatch(ServiceEvents::POST_CONFIGURE, new ConfigEvent($resolver, $this->options));
    }
}
