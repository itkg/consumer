<?php

namespace Itkg\Consumer\Authentication\Provider;

use fkooman\Guzzle\Plugin\BearerAuth\BearerAuth;
use fkooman\OAuth\Client\AccessToken;
use fkooman\OAuth\Client\Api;
use fkooman\OAuth\Client\Callback;
use fkooman\OAuth\Client\ClientConfig;
use fkooman\OAuth\Client\Context;
use fkooman\OAuth\Client\Scope;
use fkooman\OAuth\Client\SessionStorage;
use Guzzle\Http\Client;
use Guzzle\Http\ClientInterface;
use Itkg\Consumer\Authentication\Provider;
use Itkg\Consumer\Authentication\ProviderInterface;
use Itkg\Consumer\Client\Rest;
use Itkg\Core\Config;

/**
 * Class OAuth2
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class OAuth2 extends Config implements ProviderInterface
{
    /**
     * Rest client
     *
     * @var \Guzzle\Http\Client
     */
    protected $client;
    /**
     * oAuth Storage
     *
     * @var \fkooman\OAuth\Client\SessionStorage
     */
    protected $storage;
    /**
     * Client config
     *
     * @var \fkooman\OAuth\Client\ClientConfig
     */
    protected $config;
    /**
     * Current context
     *
     * @var Context
     */
    protected $context;
    /**
     * oAuth2 api
     * @var Api
     */
    protected $api;
    /**
     * Callback redirect url
     *
     * @var string
     */
    protected $redirect;

    /**
     * List of required parameters
     *
     * @var array
     */
    protected $requiredParams = array(
        'id',
        'client_id',
        'client_secret',
        'scope',
        'token_endpoint',
        'authorize_endpoint',
        'redirect_uri'
    );

    /**
     * Constructor
     *
     * @param array $params List of params
     * @param null $storage Specific storage
     */
    public function __construct($params = array(), $storage = null)
    {
        $this->setParams($params);

        $this->validateParams();
        if (!empty($storage)) {
            $this->storage = $storage;
        } else {
            $this->storage = new SessionStorage();
        }

        $this->client = new Client();
        $this->config = new ClientConfig($this->params);
        $this->createContext();
        $this->createApi();
    }


    /**
     * Create a context
     */
    public function createContext()
    {
        $this->context = new Context($this->getParam('user_id'), new Scope($this->getParam('scope')));
    }

    /**
     * Create an api
     */
    public function createApi()
    {
        $this->api = new Api(
            $this->params['id'],
            $this->config,
            $this->storage,
            $this->client
        );
    }

    /**
     * Getter accessToken
     * Authenticate if no accessToken exist
     *
     * @return AccessToken
     */
    public function getAccessToken()
    {
        $accessToken = $this->authenticate();

        return $accessToken;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate()
    {

        if (false === ($accessToken = $this->api->getAccessToken($this->context))) {
            $this->saveState();
            header("HTTP/1.1 302 Found");
            header("Location: " . $this->api->getAuthorizeUri($this->context));
            exit;
        }
        $this->isLogged = true;

        return $accessToken;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAccess()
    {
        return (false !== $this->getAccessToken());
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthToken()
    {
        return array(
            'authentication' => 'oauth2',
            'access_token' => $this->getAccessToken()->getAccessToken(),
            'type' => $this->getAccessToken()->getTokenType()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function hydrateClient($client)
    {
        $client->addSubscriber(new BearerAuth($this->getAccessToken()->getAccessToken()));
    }

    /**
     * Save current state into session
     */
    public function saveState()
    {
        $this->redirect = $_SERVER['REQUEST_URI'];
        $_SESSION['itkg_consumer_oauth2'] = $this;
    }

    /**
     * Handle callback action
     *
     * @param array $data
     */
    public function handleCallback($data = array())
    {
        $cb = new Callback($this->getParam('id'), $this->config, $this->storage, $this->client);
        $cb->handleCallback($data);

        header("HTTP/1.1 302 Found");
        header("Location: " . $this->redirect);
        exit;
    }

    /**
     * {@inheritdoc}
     */
    public function clean()
    {
        $this->api->deleteAccessToken($this->context);
        $this->api->deleteRefreshToken($this->context);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeData(array $data = array())
    {
        //@TODO
    }
}