<?php

namespace Itkg\Consumer;

use Itkg\Cache\Manager as CacheManager;
use Itkg\Consumer\Authentication\ProviderInterface;
use Itkg\Consumer\Cache\Object;
use Itkg\Consumer\ClientInterface;
use Itkg\Consumer\Request;
use Itkg\Consumer\Response;
use Itkg\Consumer\Service\Event\FilterServiceEvent;
use Itkg\Consumer\Service\Events;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Service
 *
 * Call service, bind request / response and authenticate
 *
 * @package Itkg\Consumer
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class Service
{
    protected $loggers;
    protected $identifier;
    protected $request;
    protected $response;
    protected $client;
    protected $exception;
    protected $eventDispatcher;
    protected $cacheManager;
    protected $cache;
    protected $fromCache;
    protected $authenticationProvider;

    public function __construct($identifier, Request $request, Response $response,
        ClientInterface $client, $loggers = array())
    {
        $this->identifier = $identifier;
        $this->request = $request;
        $this->response = $response;
        $this->client = $client;
        $this->loggers = $loggers;
    }

    public function before($datas = array())
    {
        $this->fromCache = false;
        // bind request
        $this->request->bind($datas);
        $this->sendEvent(Events::BIND_REQUEST);

        // init client & loggers
        $this->client->init($this->request);

        $this->authenticate();

        $this->initLoggers();
    }

    public function call($datas = array())
    {
        if($this->hasCache()) {
            $this->fromCache = true;
            return $this->cacheCall($datas);
        }

        return $this->directCall($datas);
    }

    public function directCall($datas = array())
    {
        // Init & bind request
        $this->before($datas);

        try {
            // Call
            $this->sendEvent(Events::PRE_CALL);
            $this->client->call();

        }catch(\Exception $e) {
            $this->exception = $e;
        }

        // Bind response into response object
        $this->after();

        // Post call event
        $this->sendEvent(Events::POST_CALL);

        // Return last response
        return $this->response;
    }

    public function cacheCall($datas = array())
    {
        // Create cache params to identify cache
        $datas[0] = $this->getIdentifier();
        $datas[1] = $this->request->getHost();
        $datas[2] = $this->request->getUri();
        $this->response = $this->cacheManager->getValueFromObject($this->cache, $datas);
        if($this->fromCache) {
            $this->sendEvent(Events::FROM_CACHE);
        }

        return $this->response;
    }

    public function authenticate()
    {

        if($this->hasAuthenticationProvider()) {
            if(!$this->authenticationProvider->hasAccess()) {
                try {
                    $this->sendEvent(Events::PRE_AUTHENTICATE);

                    $this->getAuthenticationProvider()->authenticate();

                }catch(\Exception $e) {

                    $this->exception = $e;

                    $this->sendEvent(Events::FAIL_AUTHENTICATE);

                    throw $e;
                }
                $this->sendEvent(Events::SUCCESS_AUTHENTICATE);
            }

            $this->authenticationProvider->hydrateClient($this->client);
        }
    }

    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function hasCache()
    {
        return (null != $this->cache);
    }

    public function setCache(Object $cache)
    {
        $this->cache = $cache;
        $this->cache->setService($this);
    }

    public function getCacheManager()
    {
        return $this->cacheManager;
    }

    public function setCacheManager(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    public function sendEvent($eventType)
    {
        $this->eventDispatcher->dispatch($eventType, new FilterServiceEvent($this));
    }

    public function after()
    {
        // If exception we throw it
        if($this->exception) {
            $this->sendEvent(Events::FAIL_CALL);
            if($this->hasAuthenticationProvider()) {

                $this->getAuthenticationProvider()->clean();
            }
            throw $this->exception;
        }

        // Bind response
        $this->response->bind($this->client->getResponse());
        $this->sendEvent(Events::BIND_RESPONSE);

        $this->sendEvent(Events::SUCCESS_CALL);
    }

    public function initLoggers()
    {
        foreach($this->getLoggers() as $logger) {
            $logger->init($this->identifier);
        }
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
        if(!is_array($this->loggers)) {
            $this->loggers = array();
        }
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

    public function getAuthenticationProvider()
    {
        return $this->authenticationProvider;
    }

    public function hasAuthenticationProvider()
    {
        return (false !== $this->authenticationProvider);
    }

    public function setAuthenticationProvider(ProviderInterface $provider)
    {
        $this->authenticationProvider = $provider;
    }
}