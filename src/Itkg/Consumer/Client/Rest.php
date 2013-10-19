<?php

namespace Itkg\Consumer\Client;

use Guzzle\Http\Client as BaseClient;
use Itkg\Consumer\ClientInterface;
use Itkg\Consumer\Request;

/**
 * Class Client
 *
 * @author Pascal DENIS <pascal.denis.75@gmail.com>
 */
class Rest extends BaseClient implements ClientInterface
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
            $httpMethod = strtoupper($this->request->getMethod());
        }else {
            $httpMethod = 'GET';
        }
        $uri = $this->request->getUri();
        $datas = $this->request->create();
        $headers = $this->request->getHeaders();
        print_r($datas);
        switch($httpMethod) {
            case 'GET':
                $uri = $this->makeUrl($uri, $datas);

                $request = $this->get($uri, $headers);
            break;
            case 'POST':
                $request = $this->post($uri, $headers, $datas);
            break;
            case 'PUT':

                $request = $this->put($uri, $headers, $datas);
            break;
            case 'DELETE':

                $request = $this->delete($uri, $headers, $datas);
            break;
        }

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
        try {
            $this->response = $request->send();
        }catch(\Exception $e) {
            print_r($e);
        }
    }

    /**
     * Construct an uri with parameters
     * et les valeurs
     *
     * @param string $url
     * @param array $datas
     * @return string
     */
    public function makeUrl($url, $datas)
    {
        $separator = '?';
        $valueSeparator = '=';
        $index = 0;
        if(is_array($datas) && !empty($datas)) {
            if(preg_match('/\\?/', $url)) {
                $index ++;
            }

            foreach($datas as $key => $value) {
                if($key != '') {
                    $currentKeySeparator = substr($key, 0, 1);
                    if(!in_array($currentKeySeparator, array('.' ,'/', '&', '?', '#'))) {
                        if($index > 0) {
                            $separator = '&';
                        }else {
                            $separator = '?';
                        }
                        $index++;
                    }else {
                        $separator = '';
                    }
                    $key = $separator.$key;
                }
                $currentValueSeparator = substr($value, 0, 1);
                if(!in_array($currentValueSeparator, array('.', '/', '&', '?', '#'))) {

                    $valueSeparator = '=';
                }else {
                    $valueSeparator = '';
                }

                $url .= $key.$valueSeparator.$value;

            }
        }
        return $url;
    }
    public function getResponse()
    {
        if($this->response) {
            return array(
                'body'   => $this->response->getBody(true),
                'header' => $this->response->getRawHeaders()
            );
        }
        print_r($this->response);
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