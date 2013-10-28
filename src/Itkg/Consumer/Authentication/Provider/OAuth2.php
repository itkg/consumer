<?php

namespace Itkg\Consumer\Authentication\Provider;

use fkooman\Guzzle\Plugin\BearerAuth\BearerAuth;
use fkooman\OAuth\Client\Api;
use fkooman\OAuth\Client\ClientConfig;
use fkooman\OAuth\Client\Context;
use fkooman\OAuth\Client\Scope;
use fkooman\OAuth\Client\SessionStorage;
use Guzzle\Http\Client;
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

    public function __construct($params = array(), $storage = null)
    {
        $this->setParams($params);

        // @TODO : Define requiredParams
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

    public function hydrateClient(Rest $client)
    {
       $this->client->addSubscriber(BearerAuth($this->getAccessToken()->getAccessToken()));
    }

    public function saveState()
    {
        $_SESSION['itkg_consumer_oauth2'] = array(
            'config'   => $this->config,
            'redirect' => $_SERVER['REQUEST_URI'],
            'id'       => $this->getParam('id')
        );
    }
}