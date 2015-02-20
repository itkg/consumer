<?php

namespace Itkg\Consumer\Authentication;
use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Itkg\Consumer\Service\ServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class OAuthAuthenticationProvider
 *
 * An OAuth authentication provider
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class OAuthAuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var \OAuth
     */
    protected $api;

    /**
     * @var bool
     */
    protected $state = 0;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $redirectUrl;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Session $session
     * @param null $request
     */
    public function __construct(Session $session, $request = null)
    {
        $this->session = $session;
        $this->request = $request;

        if (null === $this->request) {
            $this->request = Request::createFromGlobals();
        }
        $this->restoreState();
    }

    /**
     * Get authenticated token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Call authorize endpoint
     */
    public function authenticate()
    {
        $this->api = new \OAuth(
            $this->options['consumer_key'],
            $this->options['consumer_secret'],
            OAUTH_SIG_METHOD_HMACSHA1,
            OAUTH_AUTH_TYPE_URI
        );

        if (!$this->request->query->has('oauth_token') && $this->state === 0) {
            $request_token_info =  $this->api->getRequestToken($this->options['request_token_endpoint']);
            $this->secret = $request_token_info['oauth_token_secret'];
            $this->state = 1;
            $this->redirectUrl = $this->request->getPathInfo();
            $this->saveState();

            header(
                sprintf(
                    'Location: %s?oauth_token=%s',
                    $this->options['authorize_endpoint'],
                    $request_token_info['oauth_token']
                )
            );
        }
    }

    /**
     * Handle callback by managing oauth token, saving oauth state and calling redirect URL
     *
     * @param Request $request
     */
    public function handleCallback(Request $request)
    {
        try {
            $this->api = new \OAuth(
                $this->options['consumer_key'],
                $this->options['consumer_secret'],
                OAUTH_SIG_METHOD_HMACSHA1,
                OAUTH_AUTH_TYPE_URI
            );

            $this->api->setToken($request->query->get('oauth_token'), $this->secret);
            $accessToken = $this->api->getAccessToken($this->options['access_token_endpoint']);

            $this->state = 2;
            $this->token = $accessToken['oauth_token'];
            $this->secret = $accessToken['oauth_token_secret'];
        }catch(\Exception $e) {
            $this->state = null;
        }

        $this->saveState();

        header('HTTP/1.1 302 Found');
        header('Location: '.$this->redirectUrl);
    }

    /**
     * Clean session data
     */
    public function clean()
    {
        $this->session->remove('itkg_consumer.oauth');
        $this->session->remove('itkg_consumer.oauth_values');
    }

    /**
     * Save state into the session
     */
    public function saveState()
    {
        $this->session->set('itkg_consumer.oauth', $this);
        $this->session->set('itkg_consumer.oauth_values', array(
            'secret' => $this->secret,
            'state' => $this->state,
            'token' => $this->token
        ));
    }

    /**
     * Load state from session
     */
    public function restoreState()
    {
        if (!$this->session->has('itkg_consumer.oauth_values')) {
            return;
        }

        $values = $this->session->get('itkg_consumer.oauth_values');
        $this->state  = $values['state'];
        $this->secret = $values['secret'];
        $this->token  = $values['token'];
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
            'consumer_key',
            'consumer_secret',
            'authorize_endpoint',
            'redirect_url',
            'access_token_endpoint',
        ));

        $this->options = $optionsResolver->resolve($options);

        return $this;
    }

    /**
     * Inject authenticated information into service components
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
        $client->addSubscriber(new OauthPlugin(array(
            'consumer_key'    => $this->options['consumer_key'],
            'consumer_secret' => $this->options['consumer_secret'],
            'token'           => $this->token,
            'token_secret'    => $this->secret
        )));
    }
}
