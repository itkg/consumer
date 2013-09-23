<?php

namespace Itkg\Consume;

use Itkg\Config;

class Service extends Config
{
    protected $exception;
    protected $lastResponse;
    protected $lastRequest;
    protected $config;

    protected function after()
    {
        // Utiliser des event
    }

    protected function before()
    {
        // Utiliser des event
    }

    protected function init()
    {

    }

    public function call($method, array $params = array())
    {
        $this->before();

        try {
            $this->callMethod($method, $params);
        }catch(\Exception $e) {
            $this->exception = $e;
        }

        $this->after();
    }

    protected function callMethod($method, array $params = array())
    {
        // TODO : injecter la factory
        $method = \Itkg::get('consume.service.method.factory')->getMethod();
        $method->getRequest()->bind($params);
        // Init client
        // TODO : injecter la factory
        $client = \Itkg::get('consume.client.factory')->getClient(
            $method->getProtocol(),
            $method->getRequest()
        );

        $client->call();
        // Gestion de cache ?
        $method->getResponse()->bind($client->getResponse());

        return $method->getResponse();
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getException()
    {
        return $this->exception;
    }

    public function setConfig(Config $config)
    {
        $this->config = $config;
    }
}