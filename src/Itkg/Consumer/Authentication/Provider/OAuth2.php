<?php

namespace Itkg\Consumer\Authentication\Provider;

use fkooman\Guzzle\Plugin\BearerAuth\BearerAuth;
use fkooman\OAuth\Client\Api;
use fkooman\OAuth\Client\ClientConfig;
use fkooman\OAuth\Client\Context;
use fkooman\OAuth\Client\Scope;
use fkooman\OAuth\Client\SessionStorage;
use fkooman\OAuth\Client\Callback;
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
class OAuth2  extends Config implements ProviderInterface
{
    protected $client;
    protected $storage;
    protected $config;
    protected $context;
    protected $api;
    protected $redirect;

    protected $requiredParams = array(
        'id',
        'client_id',
        'client_secret',
        'scope',
        'token_endpoint',
        'authorize_endpoint',
        'redirect_uri'
    );

    public function __construct($params = array(), $storage = null)
    {
        $this->setParams($params);

        $this->validateParams();
        if(!empty($storage)) {
            $this->storage = $storage;
        }else {
            $this->storage = new SessionStorage();
        }

        $this->client = new Client();
        $this->config = new ClientConfig($this->params);
        $this->createContext();
        $this->createApi();
    }


    public function createContext()
    {
        $this->context = new Context($this->getParam('user_id'), new Scope($this->getParam('scope')));
    }

    public function createApi()
    {
        $this->api = new Api(
            $this->params['id'],
            $this->config,
            $this->storage,
            $this->client
        );
    }

    public function getAccessToken()
    {
        $accessToken = $this->authenticate();

        return $accessToken;
    }

    public function authenticate()
    {

       if(false === ($accessToken = $this->api->getAccessToken($this->context))) {
            $this->saveState();
            header("HTTP/1.1 302 Found");
            header("Location: " . $this->api->getAuthorizeUri($this->context));
            exit;
        }
        $this->isLogged = true;

        return $accessToken;
    }

    public function hasAccess()
    {
        return (false !== $this->getAccessToken());
    }

    public function getAuthToken()
    {
        return array(
            'authentication' => 'oauth2',
            'access_token' => $this->getAccessToken()->getAccessToken(),
            'type' => $this->getAccessToken()->getTokenType()
        );
    }

    public function hydrateClient($client)
    {
       $client->addSubscriber(new BearerAuth($this->getAccessToken()->getAccessToken()));
    }

    public function saveState()
    {
        $this->redirect = $_SERVER['REQUEST_URI'];
        $_SESSION['itkg_consumer_oauth2'] = $this;
    }

    public function handleCallback($data = array())
    {
        $cb = new Callback($this->getParam('id'), $this->config, $this->storage, $this->client);
        $cb->handleCallback($data);

        header("HTTP/1.1 302 Found");
        header("Location: ".$this->redirect);
        exit;
    }

    public function clean()
    {
        $this->api->deleteAccessToken($this->context);
        $this->api->deleteRefreshToken($this->context);
    }

    public function mergeData(array $data = array())
    {
        //@TODO
    }
}