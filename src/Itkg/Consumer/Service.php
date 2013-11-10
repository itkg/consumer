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
    /**
     * List og loggers
     *
     * @var array
     */
    protected $loggers;
    /**
     * Service identifier
     *
     * @var string
     */
    protected $identifier;
    /**
     * Request model
     *
     * @var Request
     */
    protected $request;
    /**
     * Response model
     *
     * @var Response
     */
    protected $response;
    /**
     * WS Client
     *
     * @var ClientInterface
     */
    protected $client;
    /**
     * Possible exception
     *
     * @var \Exception
     */
    protected $exception;
    /**
     * Event dispatcher
     *
     * @var EventDispatcher
     */
    protected $eventDispatcher;
    /**
     * Cache manager
     *
     * @var CacheManager
     */
    protected $cacheManager;
    /**
     * Cache object
     *
     * @var \Itkg\Cache\Object
     */
    protected $cache;
    /**
     * Call from cache
     * 
     * @var boolean
     */
    protected $fromCache;
    /**
     * Authentication Provider
     * 
     * @var Authentication\ProviderInterface
     */
    protected $authenticationProvider;

    /**
     * Constructor
     *
     * @param string $identifier Service Identifier
     * @param Request $request Request model
     * @param Response $response Response model
     * @param ClientInterface $client WS client
     * @param array $loggers List of loggers
     */
    public function __construct(
        $identifier,
        Request $request,
        Response $response,
        ClientInterface $client,
        $loggers = array()
    ) {
        $this->identifier = $identifier;
        $this->request = $request;
        $this->response = $response;
        $this->client = $client;
        $this->loggers = $loggers;
    }

    /**
     * This method is called before call method
     * Init service :
     * - Bind Request
     * - Init Client
     * - Authenticate service
     * - Init loggers
     *
     * @param array $data List of params
     */
    public function before($data = array())
    {
        $this->fromCache = false;
        // bind request
        $this->request->bind($data);
        $this->sendEvent(Events::BIND_REQUEST);

        // init client & loggers
        $this->client->init($this->request);
        // authenticate request before call
        $this->authenticate();

        $this->initLoggers();
    }

    /**
     * Call method (WS Call)
     * - Cache call or Direct call
     *
     * @param array $data List of params
     * @return Response
     */
    public function call($data = array())
    {
        if ($this->hasCache()) {
            $this->fromCache = true;
            // Use cache object to make call
            return $this->cacheCall($data);
        }
        // Direct call
        return $this->directCall($data);
    }

    /**
     * Direct call
     *
     * @param array $data List of params
     * @return Response
     */
    public function directCall($data = array())
    {
        // Init & bind request
        $this->before($data);

        try {
            // Call
            $this->sendEvent(Events::PRE_CALL);
            $this->client->call();

        } catch (\Exception $e) {
            $this->exception = $e;
        }

        // Bind response into response object
        $this->after();

        // Post call event
        $this->sendEvent(Events::POST_CALL);

        // Return last response
        return $this->response;
    }

    /**
     * Cache call (Use cache obkect to store Response)
     *
     * @param array $data List of params
     * @return mixed
     */
    public function cacheCall($data = array())
    {
        // Create cache params to identify cache
        $datas[0] = $this->getIdentifier();
        $datas[1] = $this->request->getHost();
        $datas[2] = $this->request->getUri();

        // Cache call from cache object
        $this->response = $this->cacheManager->getValueFromObject($this->cache, $data);
        if ($this->fromCache) {
            $this->sendEvent(Events::FROM_CACHE);
        }

        return $this->response;
    }

    /**
     * Authenticate service (only if authenticationProvider is defined)
     *
     * @throws \Exception
     */
    public function authenticate()
    {

        if ($this->hasAuthenticationProvider()) {
            // If no access for this request
            if (!$this->authenticationProvider->hasAccess()) {
                try {
                    $this->sendEvent(Events::PRE_AUTHENTICATE);

                    // Merge Request data into provider (user key, id, etc.)
                    $this->getAuthenticationProvider()->mergeData($this->request->getData());

                    // Start authentication
                    $this->getAuthenticationProvider()->authenticate();

                } catch (\Exception $e) {

                    $this->exception = $e;

                    $this->sendEvent(Events::FAIL_AUTHENTICATE);

                    throw $e;
                }
                $this->sendEvent(Events::SUCCESS_AUTHENTICATE);
            }

            // Hydrate client with credentials
            $this->authenticationProvider->hydrateClient($this->client);
        }
    }

    /**
     * Setter EventDispatcher
     *
     * @param EventDispatcher $eventDispatcher An event dispatcher
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Getter EventDispatcher
     *
     * @return EventDispatcher|null
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * Getter Cache object
     *
     * @return \Itkg\Cache\Object|null
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Is cache defined?
     *
     * @return bool
     */
    public function hasCache()
    {
        return (null != $this->cache);
    }

    /**
     * Setter Cache object
     *
     * @param Object $cache A cache object
     */
    public function setCache(Object $cache)
    {
        $this->cache = $cache;
        $this->cache->setService($this);
    }

    /**
     * Getter Cache manager
     *
     * @return CacheManager|null
     */
    public function getCacheManager()
    {
        return $this->cacheManager;
    }

    /**
     * Setter Cache manager
     *
     * @param CacheManager $cacheManager A cache manager
     */
    public function setCacheManager(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * Dispatch event
     *
     * @param $eventType Event type to dispatch
     */
    public function sendEvent($eventType)
    {
        $this->eventDispatcher->dispatch($eventType, new FilterServiceEvent($this));
    }

    /**
     * After call method
     * - Clean authentication provider
     * - Bind response
     *
     * @throws \Exception
     */
    public function after()
    {
        // If exception we throw it
        if ($this->exception) {
            $this->sendEvent(Events::FAIL_CALL);
            if ($this->hasAuthenticationProvider()) {
                // To avoid some pb with authentication provider storage, we clean it
                $this->getAuthenticationProvider()->clean();
            }
            throw $this->exception;
        }

        // Bind response
        $this->response->bind($this->client->getResponse());
        $this->sendEvent(Events::BIND_RESPONSE);

        $this->sendEvent(Events::SUCCESS_CALL);
    }

    /**
     * Init loggers
     */
    public function initLoggers()
    {
        foreach ($this->getLoggers() as $logger) {
            $logger->init($this->identifier);
        }
    }

    /**
     * Getter identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Setter identifier
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Getter request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Setter request
     * @param Request $request A request model
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Getter response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Setter response
     *
     * @param Response $response A response model
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Getter loggers
     *
     * @return array
     */
    public function getLoggers()
    {
        if (!is_array($this->loggers)) {
            $this->loggers = array();
        }
        return $this->loggers;
    }

    /**
     * Setter loggers
     *
     * @param array $loggers List of loggers
     */
    public function setLoggers(array $loggers)
    {
        $this->loggers = $loggers;
    }

    /**
     * Getter client
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Setter client
     *
     * @param ClientInterface $client A WS Client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Getter exception
     *
     * @return mixed
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Setter Exception
     *
     * @param \Exception $exception
     */
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * Getter authentication provider
     *
     * @return Authentication\ProviderInterface
     */
    public function getAuthenticationProvider()
    {
        return $this->authenticationProvider;
    }

    /**
     * Is authenticationProvider defined?
     *
     * @return bool
     */
    public function hasAuthenticationProvider()
    {
        return (false !== $this->authenticationProvider);
    }

    /**
     * Setter authenticationProvider
     *
     * @param ProviderInterface $provider An authentication Provider
     */
    public function setAuthenticationProvider(ProviderInterface $provider)
    {
        $this->authenticationProvider = $provider;
    }
}