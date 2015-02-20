<?php

namespace Itkg\Consumer\Authentication;

use fkooman\Guzzle\Plugin\BearerAuth\BearerAuth;
use fkooman\OAuth\Client\AccessToken;
use fkooman\OAuth\Client\Api;
use fkooman\OAuth\Client\Callback;
use fkooman\OAuth\Client\ClientConfig;
use fkooman\OAuth\Client\Context;
use fkooman\OAuth\Client\SessionStorage;
use Guzzle\Http\Client;
use Itkg\Consumer\Service\ServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GuzzleOAuth2OAuthenticationProvider
 *
 * Provide oauth2 authentication for a guzzle based client
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class GuzzleOAuth2AuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var SessionStorage
     */
    protected $storage;

    /**
     * @var ClientConfig
     */
    protected $config;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Api
     */
    protected $api;

    /**
     * @var string
     */
    protected $redirectUrl;
    /**
     * @var bool
     */
    protected $logged = false;
    /**
     * @var array
     */
    protected $options;

    /**
     * @var AccessToken
     */
    protected $token;

    /**
     * @var Session
     */
    protected $session;
    /**
     * @var Request
     */
    protected $request;

    /**
     * @param array $options
     * @param Session $session
     * @param Request $request
     */
    public function __construct(array $options, Session $session, Request $request = null)
    {
        $this->request = $request;
        $this->session = $session;

        if (null === $this->request) {
            $this->request = Request::createFromGlobals();
        }

        $this->storage = new SessionStorage();
        $this->client = new Client();

        $this->configure($options);
        $this->config = new ClientConfig($this->options);
        $this->createContext();
        $this->createApi();
    }

    /**
     * Create a context for a user / scope
     */
    public function createContext()
    {
        $this->context = new Context($this->options['user_id'], array($this->options['scope']));
    }

    /**
     * Create a new API from config / storage
     */
    public function createApi()
    {
        $this->api = new Api(
            $this->options['id'],
            $this->config,
            $this->storage,
            $this->client
        );
    }

    /**
     * Authentication process
     *
     * Create access token
     */
    public function authenticate()
    {
        if (false === ($accessToken = $this->api->getAccessToken($this->context))) {
            $this->saveState();
            header("HTTP/1.1 302 Found");
            header("Location: " . $this->api->getAuthorizeUri($this->context));
            exit;
        }
        $this->logged = true;

        $this->token = $accessToken;
    }

    /**
     * Save state into the session
     */
    public function saveState()
    {
        $this->redirectUrl = $this->request->getPathInfo();
        $this->session->set('itkg_consumer.oauth2', $this);
    }

    /**
     * Handle callback authentication
     *
     * @param Request $request
     */
    public function handleCallback(Request $request)
    {
        $cb = new Callback($this->options['id'], $this->config, $this->storage, $this->client);
        $cb->handleCallback($request->query->all());

        header("HTTP/1.1 302 Found");
        header("Location: " . $this->redirectUrl);
        exit;
    }

    /**
     * Clean api tokens
     */
    public function clean()
    {
        $this->api->deleteAccessToken($this->context);
        $this->api->deleteRefreshToken($this->context);
    }

    /**
     * Configure provider options
     *
     * @param array $options
     *
     * @return $this
     */
    public function configure(array $options)
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver->setRequired(array(
            'id',
            'client_id',
            'client_secret',
            'scope',
            'token_endpoint',
            'authorize_endpoint',
            'redirect_uri'
        ));

        $this->options = $optionsResolver->resolve($options);

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        if (null == $this->token) {
            return null;
        }
        return $this->token->getAccessToken();
    }

    /**
     * Hydrate service client with oauth2 data
     *
     * @param ServiceInterface $service
     */
    public function hydrate(ServiceInterface $service)
    {
        $this->hydrateClient($service->getClient());
    }

    /**
     * @param Client $client
     */
    private function hydrateClient(Client $client)
    {
        $client->addSubscriber(new BearerAuth($this->getToken()));
    }
}
