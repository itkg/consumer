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
            $response = $this->callMethod($method, $params);
        }catch(\Exception $e) {
            $this->exception = $e;
            $response = null;
        }

        $this->after();

        return $response;
    }

    protected function callMethod($method, array $params = array())
    {
        // @TODO : injecter le container
        $method = \Itkg::get($method);

        $method->getRequest()->bind($params);
        $this->lastRequest = $method->getRequest();
        // Init client
        // TODO : injecter la factory
        $method->call();

        $this->lastResponse = $method->getResponse();

        return $this->lastResponse;
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