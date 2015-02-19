<?php

namespace Itkg\Consumer\Service;

use Itkg\Consumer\Client\ClientInterface;
use Itkg\Consumer\Event\ConfigEvent;
use Itkg\Consumer\Event\ServiceEvent;
use Itkg\Consumer\Event\ServiceEvents;
use Itkg\Core\CacheableInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Itkg\Consumer\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Service
 *
 * A minimal service for sending requests & handle responses
 *
 * @package Itkg\Consumer\Service
 */
class Service implements ServiceInterface, CacheableInterface
{
    /**
     * @var bool
     */
    private $loaded = false;
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
     * @param EventDispatcher $eventDispatcher
     * @param ClientInterface $client
     * @param array $options
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
     * @param Request $request
     * @param Response $response
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
     * Get option by key
     *
     * @param $key
     *
     * @return mixed
     */
    public function getOption($key)
    {
        return $this->options[$key];
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasOption($key)
    {
        return isset($this->options[$key]);
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
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Response $response
     *
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return Response
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
     * Hash key getter
     *
     * @return string
     */
    public function getHashKey()
    {
        return strtr($this->getIdentifier(), ' ','_').md5(
            sprintf(
                '%s_%s_%s',
                $this->request->getContent(),
                $this->request->getUri(),
                json_encode($this->request->headers->all())
            )
        );
    }

    /**
     * Get cache TTL
     *
     * @return int
     */
    public function getTtl()
    {
        return $this->options['cache_ttl'];
    }

    /**
     * Return if object is already loaded from cache
     *
     * @return bool
     */
    public function isLoaded()
    {
        return $this->loaded;
    }

    /**
     * Set is loaded
     *
     * @param bool $isLoaded
     */
    public function setIsLoaded($isLoaded = true)
    {
        $this->loaded = $isLoaded;
    }

    /**
     * Get data from service for cache set
     *
     * @return mixed
     */
    public function getDataForCache()
    {
        return call_user_func(
            $this->options['cache_serializer'],
            $this->response
        );
    }

    /**
     * Restore data after cache load
     *
     * @param $data
     * @return $this
     */
    public function setDataFromCache($data)
    {
        $this->response = call_user_func(
            $this->options['cache_unserializer'],
            $data
        );
    }

    /**
     * Manage configuration
     *
     * @param array $options
     * @param OptionsResolver $resolver
     */
    protected function configure(array $options = array(), OptionsResolver $resolver = null)
    {
        if (null === $resolver) {
            $resolver = new OptionsResolver();
        }

        $this->eventDispatcher->dispatch(ServiceEvents::PRE_CONFIGURE, new ConfigEvent($resolver, $options));

        $resolver
            ->setRequired('identifier')
            ->setDefined(array(
                'logger'
            ))
            ->addAllowedTypes(array(
                    'logger'    => 'Psr\Log\LoggerInterface',
                    'cache_ttl' => array('null', 'int'),
                    'cacheable' => 'bool',
                    'loggable'  => 'bool'
                )
            );

        $this->setDefaultOptions($resolver);

        $this->options = $resolver->resolve($options);

        $this->eventDispatcher->dispatch(ServiceEvents::POST_CONFIGURE, new ConfigEvent($resolver, $this->options));
    }

    /**
     * Configure default options
     *
     * @param OptionsResolver $resolver
     * @return $this
     */
    protected function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'response_format'    => 'json', // Define a format used by serializer (json, xml, etc),
                'response_type'      => 'array', // Define a mapped class for response content deserialization,
                'loggable'           => false,
                'cacheable'          => false,
                'cache_ttl'          => null,
                'cache_serializer'   => 'serialize',
                'cache_unserializer' => 'unserialize'
            ));

        return $this;
    }
}
