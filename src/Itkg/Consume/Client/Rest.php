<?php

namespace Itkg\Consume\Rest;

use Guzzle\Http\Client as BaseClient;
use Itkg\Consume\ClientInterface;
use Itkg\Consume\Model\Request;

/**
 * Class Client
 *
 * @author Pascal DENIS <pascal.denis.75@gmail.com>
 */
class Client extends BaseClient implements ClientInterface
{
    protected $config;
    protected $response;
    protected $request;

    public function init(Request $request)
    {
        $this->request = $request;

        parent::__construct(
            $this->request->getHost()
           // $this->method->getOptions() // TODO : ou stocker les options ?
        );
    }

    public function call()
    {
        if($this->request->getMethod()) {
            $httpMethod = strtolower($this->request->getMethod());
        }else {
            $httpMethod = 'get';
        }

        $this->request = $httpMethod(
            $this->request->getUri(),
            $this->request->getHeaders(),
            $this->request->create()
        );
        // Ou stocker ces éléments
/*        if($this->config->hasOption('login') && $this->config->hasOption('password')) {
            $request->setAuth(
                $this->config->getOption('login'),
                $this->config->getOption('password')
            );
        } */
        // Ou stocker ces éléments
        // Si des cookies sont présents, on les ajoute à la requete
      /*  if($this->config->hasOption('cookies') && is_array($cookies = $this->config->getOption('cookies'))) {
            foreach($cookies as $key => $value) {
                $request->addCookie($key, $value);
            }
        } */

        $this->response = $this->request->send();
    }

    public function getResponse()
    {
        if($this->response) {
            return array(
                'body'   => $this->response->getBody(true),
                'header' => $this->response->getRawHeaders()
            );
        }
        return null;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @see Guzzle\Http\Client::execute($command)
     */
    public function execute($command)
    {

    }

    /**
     * @see Guzzle\Http\Client::getCommand($name, $args)
     */
    public function getCommand($name, array $args = array())
    {

    }

    /**
     * @see Guzzle\Http\Client::getDescription
     */
    public function getDescription()
    {

    }

    /**
     * @see Guzzle\Http\Client::getInflector()
     */
    public function getInflector()
    {

    }

    /**
     * @see Guzzle\Http\Client::getIterator($command, $commandOptions, $iteratorOptions)
     */
    public function getIterator($command, array $commandOptions = null, array $iteratorOptions = array())
    {

    }

    /**
     * @see Guzzle\Http\Client::setCommandFactory($factory)
     */
    public function setCommandFactory(CommandFactoryInterface $factory)
    {

    }

    /**
     * @see Guzzle\Http\Client::setDescription($service, $updateFactory)
     */
    public function setDescription(ServiceDescription $service, $updateFactory = true)
    {

    }

    /**
     * @see Guzzle\Http\Client::setInflector($inflector)
     */
    public function setInflector(InflectorInterface $inflector)
    {

    }

    /**
     * @see Guzzle\Http\Client::setResourceIteratorFactory($factory)
     */
    public function setResourceIteratorFactory(ResourceIteratorFactoryInterface $factory)
    {

    }

    /**
     * @see Guzzle\Http\Client::factory($config)
     */
    public static function factory($config = array())
    {

    }
}