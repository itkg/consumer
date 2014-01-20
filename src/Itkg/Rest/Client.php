<?php

namespace Itkg\Rest;

use Guzzle\Http\Client as BaseClient;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Plugin\History\HistoryPlugin;

/**
 * Client REST
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @package \Itkg\Rest
 */
class Client extends BaseClient
{
    protected $options;

    protected $history;

    /**
     * Constructeur
     *
     * @param string $host
     * @param array $options
     */
    public function __construct($host, array $options = array())
    {
        $this->options = $options;
        $this->history = new HistoryPlugin();
        $this->history->setLimit(5);

        $this->addSubscriber($this->history);
        parent::__construct($host, $options);
    }

    public function __getLastRequest()
    {
        return $this->history->getLastRequest();
    }

    public function __getLastResponse()
    {
        $nbRequests = $this->history->count();
        if ($nbRequests > 0) {
            return $this->history->getLastResponse();
        } else {
            return null;
        }
    }

    /**
     * Méthode commune aux appels GET | POST | PUT | DELETE
     *
     * Traitement des options après merge
     * Gère les cookies, les headers
     * @codeCoverageIgnore
     * @param string $method
     * @param string $uri
     * @param array $data (Les données à envoyer)
     * Ces données seront ensuite traitées en fonction des cas pour correspondre
     * au format attendu par les différentes méthodes
     *
     * @param array|\Itkg\Rest\type $options Les options possibles (headers, cookies)
     * @return array('body', 'headers')
     */
    public function call($method, $uri, $data = array(), $options = array())
    {
        $request = null;
        $this->addOptions($options);
        $headers = null;
        if ($this->options['headers']) {
            $headers = $this->options['headers'];
        }
        $request = $this->getRequest($method, $uri, $data, $headers);
        if ($request) {
            // Si login et password, on procède à l'authentification
            $request = $this->hydrateRequest($request);

            // Envoi de la requete
            $response = $request->send();
            $aResponseDatas = array();
            // Récupération du header
            $aResponseDatas['headers'] = $response->getMessage();
            // Récupération du body
            $aResponseDatas['body'] = $response->getBody(true);

            return $aResponseDatas;
        }
        return null;
    }

    /**
     * Construit une URL à partir d'un tableau de données en associant les clés
     * et les valeurs
     *
     * @param string $url
     * @param array $data
     * @return string
     */
    public function makeUrl($url, $data)
    {
        $index = 0;
        if (is_array($data) && !empty($data)) {
            if (preg_match('/\\?/', $url)) {
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

                $url .= $key . $valueSeparator . $value;

            }
        }
        return $url;
    }

    /**
     * Ajoute des options à celles déja existantes
     *
     * @param array $options
     */
    public function addOptions(array $options = array())
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Get request for method & params
     * @codeCoverageIgnore
     * @param string $method request method
     * @param string $uri request uri
     * @param array $data request params
     * @param array $headers Request headers
     * @return \Guzzle\Http\Message\RequestInterface|null
     */
    protected function getRequest($method = 'GET', $uri = '', $data = array(), $headers = array())
    {
        switch ($method) {
            case 'GET':
                $uri = $this->makeUrl($uri, $data);

                return $this->get($uri, $headers);
                break;
            case 'POST':

                return $this->post($uri, $headers, $data);
                break;
            case 'PUT':

                return $this->put($uri, $headers, $data);
                break;
            case 'DELETE':

                return $this->delete($uri, $headers, $data);
                break;
        }
        return null;
    }

    /**
     * Hydrate request with client options
     * @codeCoverageIgnore
     * @param RequestInterface $request
     * @return RequestInterface
     */
    protected function hydrateRequest(RequestInterface $request)
    {
        if (isset($this->options['login']) && isset($this->options['password'])) {
            $request->setAuth($this->options['login'], $this->options['password']);
        }

        // Si des cookies sont présents, on les ajoute à la requete
        if (isset($this->options['cookies']) && is_array($this->options['cookies'])) {
            foreach ($this->options['cookies'] as $key => $value) {
                $request->addCookie($key, $value);
            }
        }

        return $request;
    }
}
