<?php

namespace Itkg\Soap;

/**
 *
 * Client SOAP
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @package \Itkg\Soap
 */
class Client extends \SoapClient
{

    /**
     * Options du client
     *
     * @var array
     */
    protected $options;

    /**
     *
     * @var \SoapHeader
     */
    protected $header;

    /**
     * Début de l'appel
     *
     * @var int
     */
    protected $start;

    /**
     * Fin de l'appel
     *
     * @var int
     */
    protected $end;

    /**
     * login de l'entête sécurisée
     *
     * @var string
     */
    protected $loginHeaderSecurity;

    /**
     * password de l'entête sécurisée
     *
     * @var string
     */
    protected $passwordHeaderSecurity;

    /**
     * trame de la requete surchargée
     *
     * @var string
     */
    protected $requestOverride;

    /**
     * Constructeur
     *
     * @param string $wsdl
     * @param array $options
     */
    public function __construct($wsdl = '', array $options = array())
    {
        $this->options = array(
            "connection_timeout"=>2,
            "trace"=>true,
            "encoding"=>"UTF8",
            "soap_version"=>SOAP_1_1,
            "features" => SOAP_SINGLE_ELEMENT_ARRAYS,
        );


        $this->options = array_merge($this->options, $options);

	// login et password htaccess/header sécurisé
	$this->loginHeaderSecurity = $this->options['login'];
	$this->passwordHeaderSecurity = $this->options['password'];
	unset($this->options['login']);
	unset($this->options['password']);

        parent::__construct($wsdl, $this->options);

    }


    /**
     * Appelle de la méthode __soapCall après initialisation des options
     *
     * Renvoie une Itkg\Soap\Exception\SoapException en cas d'erreur
     *
     * @param type $method
     * @param type $request
     */
    public function call($method, $request)
    {
        $this->checkRequiredOptions($method);
        
        try {
            // Set timeout
            if(isset($this->options['timeout']) && is_numeric($this->options['timeout'])) {
                $currentTimeout = ini_get('default_socket_timeout');
                ini_set('default_socket_timeout', $this->options['timeout']);
            }

            // Entete
            $this->makeHeaders();

            // Client SOAP
            $oSoapRequest = new \SoapVar($request, XSD_ANYXML);
            if(isset($this->options['location'])) {
                $this->__setLocation($this->options['location']);
            }
            // Appel Soap de la methode
            $this->start = microtime(true);

            $oResponse = $this->__soapCall($method, array($oSoapRequest), $this->options, $this->header);
            $this->end = microtime(true);

            // Remise en place du timeout
            if($currentTimeout) {
                ini_set('default_socket_timeout', $currentTimeout);
            }
        
        }catch(\SoapFault $e) {
            $exception = new \Itkg\Soap\Exception\SoapException($e->faultcode, $e->getMessage());
            $exception->setTrame($this->__getLastRequest());

            throw $exception;
        }

        return $oResponse;
    }

    /**
     * Début de l'appel
     *
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Fin de l'appel
     *
     * @return int
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Durée de l'appel
     *
     * @return int
     */
    public function getDuration()
    {
        return ($this->end - $this->start);
    }

    /**
     * Set la liste des options
     *
     * @param array $options
     */
    public function setOptions(array $options = array())
    {
        $this->options = $options;
    }


    /**
     * Génère le header depuis les options passées au client
     *
     */
    public function makeHeaders()
    {
        $namespace = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd";
        if(isset($this->options['namespace'])) {
            $namespace = $this->options['namespace'];
        }
        $sMust = '';
        if(isset($this->options['mustunderstand']) && $this->options['mustunderstand']) {
            $sMust = 'SOAP-ENV:mustUnderstand="1"';
        }
        if($this->loginHeaderSecurity != '' && $this->passwordHeaderSecurity != '') {

            $sHeader = '<wsse:Security '.$sMust.' xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
				<wsse:UsernameToken wsu:Id="UsernameToken-6868426" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
					<wsse:Username>'.$this->loginHeaderSecurity.'</wsse:Username>
					<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$this->passwordHeaderSecurity.'</wsse:Password>
				</wsse:UsernameToken>
			</wsse:Security>';
            if($this->options['signature'] && $this->options['signature_ns']) {
                $sHeader .='<Signature xmlns="'.$this->options['signature_ns'].'">'.$this->options['signature'].'</Signature>';
            }
            $authvars = new \SoapVar($sHeader, XSD_ANYXML, NULL, $namespace);

            
            $this->header = new \SoapHeader($namespace, "Security", $authvars);
        }
    }
    
    /**
     * Vérifie les paramêtres requis pour un appel soap
     * 
     * @param string $method
     * @throws \Itkg\Exception\NotFoundException 
     */
    protected function checkRequiredOptions($method)
    {
        // Les timeout doivent être définis pour chaque client Soap
        if(!isset($this->options['timeout']) || empty($this->options['timeout'])) {
            throw new \Itkg\Exception\NotFoundException('Paramêtre timeout non défini pour le WS '.$method);
        }
    }

    /**
     * Implémente plusieurs namespace dans la trame XML de requete SOAP
     *
     * @param string $request
     * @param string $location
     * @param string $action
     * @param string $version
     * @param int $one_way [optional]
     * If one_way is set to 1, this method returns nothing.
     * Use this where a response is not expected.
     * @return string
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0) 
    {
	if(isset($this->options['namespaces'])) {
            $namespaces = $this->options['namespaces'];
	    $compteurNamespace = 2; //commence à 2 car les deux premiers namespace sont déclarés avant

	    if(is_array($namespaces)&& !empty($namespaces)){
		foreach($namespaces as $namespace){
		    $compteurNamespace++;
		    $request = str_replace( '><SOAP-ENV:Header>',' xmlns:ns'.$compteurNamespace.'="'.$namespace.'"><SOAP-ENV:Header>' , $request);
		}
	    }
	    $this->requestOverride = $request;
        }
	return parent::__doRequest($request, $location, $action, $version, $one_way = 0);
    }

    /**
     * Récupére la trame XML de requete
     *
     * @return string
     */
    public function __getLastRequest() 
    {
	if($this->requestOverride){
	    return $this->requestOverride;
	}else{
	    return parent::__getLastRequest();
	}
    }
}
