<?php

namespace Itkg\Rest;

use Guzzle\Http\Client as BaseClient;
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
    
    public function __getLastRequest(){
        return $this->history->getLastRequest();
    }
    
    public function __getLastResponse(){
        $nbRequests = $this->history->count();
        if($nbRequests > 0) {
            return $this->history->getLastResponse();
        } else {
            return NULL;
        }
    }
    /**
     * Méthode commune aux appels GET | POST | PUT | DELETE
     * 
     * Traitement des options après merge 
     * Gère les cookies, les headers
     * 
     * @param string $method
     * @param string $uri
     * @param array $datas (Les données à envoyer)
     * Ces données seront ensuite traitées en fonction des cas pour correspondre
     * au format attendu par les différentes méthodes
     * 
     * @param type $options Les options possibles (headers, cookies)
     * @return array('body', 'headers')
     */
    public function call($method, $uri, $datas = array(), $options = array())
    {
        $request = null;
        // @TODO : gestion des exceptions
        $this->addOptions($options);
        $headers = null;
        if($this->options['headers']) {
            $headers = $this->options['headers'];
        }
        switch($method)
        {
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
        if($request) {
            // Si login et password, on procède à l'authentification
            if(isset($this->options['login']) && isset($this->options['password'])) {
                $request->setAuth($this->options['login'], $this->options['password']);
            }
            
            // Si des cookies sont présents, on les ajoute à la requete
            if(isset($this->options['cookies']) && is_array($this->options['cookies'])) {
                foreach($this->options['cookies'] as $key => $value) {
                    $request->addCookie($key, $value);
                }
            }
            
            // Envoi de la requete
            $response = $request->send();
            
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
