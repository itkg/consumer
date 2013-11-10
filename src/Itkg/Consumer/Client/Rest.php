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
    /**
     * Response values
     *
     * @var mixed
     */
    protected $response;
    /**
     * Request object
     *
     * @var Request
     */
    protected $request;
    /**
     * List of options
     *
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param array $options List of options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    /**
     * Init client with request
     *
     * @param Request $request A request object
     */
    public function init(Request $request)
    {
        $this->request = $request;

        parent::__construct(
            $this->request->getHost(),
            $this->options
        );
    }

    /**
     * Call Rest
     */
    public function call()
    {
        if ($this->request->getMethod()) {
            $httpMethod = strtoupper($this->request->getMethod());
        } else {
            $httpMethod = 'GET';
        }
        $uri = $this->request->getUri();
        $datas = $this->request->create();
        $headers = $this->request->getHeaders();

        switch ($httpMethod) {
            case 'POST':
                $request = $this->post($uri, $headers, $datas);
                break;
            case 'PUT':

                $request = $this->put($uri, $headers, $datas);
                break;
            case 'DELETE':

                $request = $this->delete($uri, $headers, $datas);
                break;
            case 'GET':
            default:
                $uri = $this->makeUrl($uri, $datas);
                $request = $this->get($uri, $headers);
                break;
        }

        if (isset($headers['login']) && isset($headers['password'])) {
            $request->setAuth(
                $headers['login'],
                $headers['password']
            );
        }

        if (isset($headers['cookies']) && is_array($headers['cookies'])) {
            foreach ($headers['cookies'] as $key => $value) {
                $request->addCookie($key, $value);
            }
        }

        $this->response = $request->send();

    }

    /**
     * Construct an uri with parameters
     * and values
     *
     * @param string $uri Request uri
     * @param array $data List of data
     * @return string
     */
    public function makeUrl($uri, $data)
    {
        $index = 0;
        if (is_array($data) && !empty($data)) {
            if (preg_match('/\\?/', $uri)) {
                $index++;
            }

            foreach ($data as $key => $value) {
                if ($key != '') {
                    $currentKeySeparator = substr($key, 0, 1);
                    if (!in_array($currentKeySeparator, array('.', '/', '&', '?', '#'))) {
                        if ($index > 0) {
                            $separator = '&';
                        } else {
                            $separator = '?';
                        }
                        $index++;
                    } else {
                        $separator = '';
                    }
                    $key = $separator . $key;
                }
                $currentValueSeparator = substr($value, 0, 1);
                if (!in_array($currentValueSeparator, array('.', '/', '&', '?', '#'))) {

                    $valueSeparator = '=';
                } else {
                    $valueSeparator = '';
                }

                $uri .= $key . $valueSeparator . $value;

            }
        }
        return $uri;
    }

    /**
     * Getter response
     *
     * @return array|null
     */
    public function getResponse()
    {
        if ($this->response) {
            return array(
                'body' => $this->response->getBody(true),
                'header' => $this->response->getRawHeaders()
            );
        }

        return null;
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