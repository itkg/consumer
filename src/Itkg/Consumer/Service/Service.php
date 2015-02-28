<?php

namespace Itkg\Consumer\Service;

use Itkg\Consumer\Client\ClientInterface;
use Itkg\Consumer\Event\ConfigEvent;
use Itkg\Consumer\Event\ServiceEvent;
use Itkg\Consumer\Event\ServiceEvents;
use Itkg\Consumer\Response;
use Itkg\Core\CacheableInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Service
 *
 * A minimal service for sending requests & handle responses
 *
 * @package Itkg\Consumer\Service
 */
class Service extends AbstractService implements ServiceInterface, ServiceConfigurableInterface,
    ServiceAuthenticableInterface, ServiceLoggableInterface, CacheableInterface
{
    /**
     * @var bool
     */
    private $loaded = false;
    /**
     * @var array
     */
    protected $options;
    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;
    /**
     * @var string|null
     */
    protected $hashKey = null;

    /**
     * @param EventDispatcher $eventDispatcher
     * @param ClientInterface $client
     * @param array $options
     */
    public function __construct(EventDispatcher $eventDispatcher, ClientInterface $client, array $options = array())
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->client = $client;

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

        if ('' === $this->response->getContent()) {
            try {
                $this->client->sendRequest($this->request, $this->response);
            } catch (\Exception $e) {
                $this->exception = $e;
                $this->eventDispatcher->dispatch(ServiceEvents::EXCEPTION, $event);

                throw $e;
            }
        }

        $this->eventDispatcher->dispatch(ServiceEvents::RESPONSE, $event);

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
     * @return string
     */
    public function getIdentifier()
    {
        return $this->options['identifier'];
    }

    /**
     * Hash key getter
     *
     * Hash key is null if service cache is disabled
     *
     * @return string
     */
    public function getHashKey()
    {
        if (!$this->getOption('cacheable')) {
            return null;
        }

        if (null === $this->hashKey) {
            $this->hashKey = strtr($this->getIdentifier(), ' ', '_') . md5(
                    sprintf(
                        '%s_%s_%s',
                        $this->request->getContent(),
                        $this->request->getUri(),
                        json_encode($this->request->headers->all())
                    )
                );
        }
        return $this->hashKey;
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
    public function configure(array $options = array(), OptionsResolver $resolver = null)
    {
        if (null === $resolver) {
            $resolver = new OptionsResolver();
        }

        $this->eventDispatcher->dispatch(ServiceEvents::PRE_CONFIGURE, new ConfigEvent($resolver, $this));

        $this->setDefaultOptions($resolver);

        $resolver
            ->setRequired('identifier')
            ->addAllowedTypes(array(
                    'logger' => array('null', 'Psr\Log\LoggerInterface'),
                    'authentication_provider' => array('null', 'Itkg\Consumer\Authentication\AuthenticationProviderInterface'),
                    'cache_ttl' => array('null', 'int'),
                    'cacheable' => 'bool',
                    'loggable' => 'bool'
                )
            );

        $this->options = $resolver->resolve($options);

        $this->eventDispatcher->dispatch(ServiceEvents::POST_CONFIGURE, new ConfigEvent($resolver, $this));
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
                'response_format' => 'json', // Define a format used by serializer (json, xml, etc),
                'response_type' => 'array', // Define a mapped class for response content deserialization,
                'loggable' => false,
                'cacheable' => false,
                'authenticable' => false,
                'cache_ttl' => null,
                'cache_serializer' => 'serialize',
                'cache_unserializer' => 'unserialize',
                'authentication_provider' => null,
                'logger' => null
            ));

        return $this;
    }

    /**
     * Authenticate service
     *
     * @return mixed
     */
    public function authenticate()
    {
        $this->getOption('authentication_provider')->authenticate();
    }

    /**
     * Service is authenticated or not
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        if (!$this->hasOption('authentication_provider')) {
            return true;
        }

        return (null !== $this->getOption('authentication_provider')->getToken());
    }

    /**
     * Inject autenticated data into the request / Client
     */
    public function makeAuthenticated()
    {
        $this->getOption('authentication_provider')->hydrate($this);
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        if (!$this->getOption('loggable')) {
            return null;
        }
        return $this->getOption('logger');
    }
}
