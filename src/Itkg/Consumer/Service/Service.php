<?php

namespace Itkg\Consumer\Service;

use Itkg\Consumer\Client\ClientInterface;
use Itkg\Consumer\Event\ConfigEvent;
use Itkg\Consumer\Event\ServiceEvent;
use Itkg\Consumer\Event\ServiceEvents;
use Itkg\Consumer\Response;
use Itkg\Core\Cache\AdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Service
 *
 * A minimal service for sending requests & handle responses
 *
 * @package Itkg\Consumer\Service
 */
class Service extends AbstractService implements AdvancedServiceInterface, ServiceConfigurableInterface,
    ServiceAuthenticableInterface, ServiceLoggableInterface, ServiceCacheableInterface
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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var string|null
     */
    protected $hashKey = null;

    /**
     * @var bool
     */
    protected $noCache = false;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param ClientInterface $client
     * @param array $options
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, ClientInterface $client, array $options = array())
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
        // reset is loaded flag before each request to allow multiple call to be cached
        $this->loaded = false;
        $this->request = $request;
        $this->response = (null === $response) ? new Response() : $response;

        $event = new ServiceEvent($this);
        $this->eventDispatcher->dispatch(ServiceEvents::REQUEST, $event);

        // check if content has been loaded by some one
        if (!$this->isLoaded() && '' === $this->response->getContent()) {
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
        $this->hashKey = strtr($this->getIdentifier(), ' ', '_') . md5(
            sprintf(
                '%s_%s_%s_%s',
                $this->request->getContent(),
                $this->request->getUri(),
                $this->canBeWarmed() ? 'warm' : '',
                json_encode($this->request->headers->all())
            )
        );
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
     * Manage configuration
     *
     * @param array $options
     * @param OptionsResolver $resolver
     *
     * @return $this
     */
    public function configure(array $options = array(), OptionsResolver $resolver = null)
    {
        if (null === $resolver) {
            $resolver = new OptionsResolver();
        }

        $this->eventDispatcher->dispatch(ServiceEvents::PRE_CONFIGURE, new ConfigEvent($resolver, $this));

        $this->setDefaultOptions($resolver);

        $resolver
            ->addAllowedTypes(array(
                    'logger'                  => array('null', 'Psr\Log\LoggerInterface'),
                    'authentication_provider' => array('null', 'Itkg\Consumer\Authentication\AuthenticationProviderInterface'),
                    'cache_ttl'               => array('null', 'int'),
                    'cache_fresh_ttl'         => array('null', 'int'),
                    'cache_warmup'            => array('false','boolean'),
                    'disabled'                => array('false','boolean'),
                    'cache_adapter'           => array('null', 'Itkg\Core\Cache\AdapterInterface'),
                )
            );

        $this->options = $resolver->resolve($options);

        $this->eventDispatcher->dispatch(ServiceEvents::POST_CONFIGURE, new ConfigEvent($resolver, $this));

        return $this;
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
                'identifier'              => 'UNDEFINED',
                'response_format'         => 'json', // Define a format used by serializer (json, xml, etc),
                'response_type'           => null, // Define a mapped class for response content deserialization,
                'cache_ttl'               => null,
                'cache_adapter'           => null,
                'cache_warmup'            => false,
                'cache_fresh_ttl'        => null,
                'authentication_provider' => null,
                'logger'                  => null,
                'disabled'                => false
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
        if (null === $this->getOption('authentication_provider')) {
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
     * Get cache adapter
     *
     * @return AdapterInterface
     */
    public function getCacheAdapter()
    {
        return $this->options['cache_adapter'];
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->getOption('logger');
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return $this->getOption('disabled');
    }

    /**
     * @return bool
     */
    public function needAuthentication()
    {
        return (null !== $this->getOption('authentication_provider'));
    }

    /**
     * @rern bool
     */
    public function canBeWarmed()
    {
        return $this->getOption('cache_warmup');
    }

    /**
     * @return int
     */
    public function getFreshTtl()
    {
        return $this->getOption('cache_fresh_ttl');
    }

    /**
     * @return bool
     */
    public function isObsolete()
    {
        if (!$this->response || null === $this->getFreshTtl()) {
            return false;
        }

        return ($this->response->getCreatedAt()->getTimestamp() - (time() - $this->getFreshTtl())) < 0;
    }

    /**
     * @param bool $noCache
     *
     * @return $this
     */
    public function setNoCache($noCache = true)
    {
        $this->noCache = $noCache;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNoCache()
    {
        return $this->noCache;
    }

    /**
     * {@inheritDoc}
     */
    public function __sleep()
    {
        return array(
            'client',
            'options',
            'request',
            'response'
        );
    }
}
