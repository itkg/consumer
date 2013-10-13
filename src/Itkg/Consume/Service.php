<?php

namespace Itkg\Consume;

use Itkg\Consume\ClientInterface;
use Itkg\Consume\Request;
use Itkg\Consume\Response;
use Itkg\Consume\Event\FilterServiceEvent;
use Itkg\Consume\Service\Events;

class Service
{
    protected $loggers;
    protected $identifier;
    protected $request;
    protected $response;
    protected $client;
    protected $exception;

    public function __construct($identifier, Request $request, Response $response, ClientInterface $client)
    {
        $this->identifier = $identifier;
        $this->request = $request;
        $this->response = $response;
        $this->client = $client;
    }

    public function validate()
    {
        // Validate method config & params ?
    }

    public function before($datas = array())
    {
        $this->request->bind($datas);
        \Itkg::get('core.event_dispatcher')->dispatch(Events::BIND_REQUEST, new FilterServiceEvent($this));

        $this->client->init($this->request);
    }

    public function call($datas = array())
    {
        $this->before($datas);

        try {
            $this->client->call();

        }catch(\Exception $e) {
            $this->exception = $e;
        }

        $this->after();

        return $this->response;
    }

    public function after()
    {
        $this->response->bind($this->client->getResponse());
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getLoggers()
    {
        return $this->loggers;
    }

    public function setLoggers(array $loggers)
    {
        $this->loggers = $loggers;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getException()
    {
        return $this->exception;
    }

    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
    }
}