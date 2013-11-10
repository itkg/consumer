<?php

namespace Itkg\Consumer\Authentication\Provider;

use Guzzle\Plugin\Oauth\OauthPlugin;
use Itkg\Consumer\Authentication\ProviderInterface;
use Itkg\Core\Config;

/**
 * Class OAuth
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class OAuth extends Config implements ProviderInterface
{
    /**
     * Client key
     *
     * @var string
     */
    protected $key;
    /**
     * Client secret
     *
     * @var string
     */
    protected $secret;
    /**
     * oAuth API
     *
     * @var \OAuth
     */
    protected $api;
    /**
     * oAuth state
     *
     * @var string
     */
    protected $state;
    /**
     * oAuth token
     *
     * @var mixed
     */
    protected $token;
    /**
     * oAuth redirect callback
     *
     * @var string
     */
    protected $redirect;

    /**
     * List of required params
     *
     * @var array
     */
    protected $requiredParams = array(
        'consumer_key',
        'consumer_secret',
        'authorize_endpoint',
        'access_token_endpoint',
    );

    /**
     * Constructor
     *
     * @param $params List of params
     */
    public function __construct($params)
    {
        $this->params = $params;

        $this->validateParams();

        $this->restoreState();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthToken()
    {
        if (false !== $this->token) {
            $this->authenticate();
        }

        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrateClient($client)
    {
        $infos = array(
            'consumer_key' => $this->getParam('consumer_key'),
            'consumer_secret' => $this->getParam('consumer_secret'),
            'token' => $this->token,
            'token_secret' => $this->secret
        );

        $client->addSubscriber(new OauthPlugin($infos));
    }

    /**
     * @{@inheritdoc}
     */
    public function hasAccess()
    {
        return (null !== $this->token);
    }

    /**
     * Create api
     */
    public function createApi()
    {
        $this->api = new \OAuth(
            $this->getParam('consumer_key'),
            $this->getParam('consumer_secret'),
            OAUTH_SIG_METHOD_HMACSHA1,
            OAUTH_AUTH_TYPE_URI
        );
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate()
    {
        $this->createApi();

        if (!isset($_GET['oauth_token']) && !$this->state) {
            $request_token_info = $this->api->getRequestToken($this->getParam('request_token_endpoint'));
            $this->secret = $request_token_info['oauth_token_secret'];
            $this->state = 1;
            $this->redirect = $_SERVER['REQUEST_URI'];
            $this->saveState();

            header(
                'Location: ' . $this->getParam(
                    'authorize_endpoint'
                ) . '?oauth_token=' . $request_token_info['oauth_token']
            );
            exit;
        }
    }

    /**
     * Heandle callback action
     *
     * @param $data List of data
     */
    public function handleCallback($data)
    {
        try {
            $this->createApi();

            $this->api->setToken($data['oauth_token'], $this->secret);
            $accessToken = $this->api->getAccessToken($this->getParam('access_token_endpoint'));

            $this->state = 2;
            $this->token = $accessToken['oauth_token'];
            $this->secret = $accessToken['oauth_token_secret'];
        } catch (\Exception $e) {
            $this->state = null;
        }
        $this->saveState();
        header("HTTP/1.1 302 Found");
        header("Location: " . $this->redirect);
        exit;

    }

    /**
     * {@inheritdoc}
     */
    public function clean()
    {
        unset($_SESSION['itkg_consumer_oauth']);
        unset($_SESSION['itkg_consumer_oauth_values']);
    }

    /**
     * Save current state
     */
    public function saveState()
    {
        $_SESSION['itkg_consumer_oauth'] = $this;
        $_SESSION['itkg_consumer_oauth_values'] = array(
            'secret' => $this->secret,
            'state' => $this->state,
            'token' => $this->token
        );
    }

    /**
     * try to restore an old state
     */
    public function restoreState()
    {
        if (isset($_SESSION['itkg_consumer_oauth_values'])) {
            $this->state = $_SESSION['itkg_consumer_oauth_values']['state'];
            $this->secret = $_SESSION['itkg_consumer_oauth_values']['secret'];
            $this->token = $_SESSION['itkg_consumer_oauth_values']['token'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function mergeData(array $data = array())
    {
        //@TODO
    }

}