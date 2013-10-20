<?php

namespace Itkg\Consumer\Client;

use Itkg\Consumer\Client\Soap\Exception\SoapException;
use Itkg\Consumer\Request;

class Soap extends \SoapClient
{

    /**
     * Options du client
     *
     * @var array
     */
    protected $options;

    /**
     * Soap header
     *
     * @var \SoapHeader
     */
    protected $header;

    /**
     * Overrided request
     *
     * @var string
     */
    protected $requestOverride;

    /**
     * Request Model
     *
     * @var Request
     */
    protected $request;

    /**
     * Soap Response
     *
     * @var \SoapResponse
     *
     */
    protected $response;

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

        parent::__construct($wsdl, $this->options);

    }

    public function init(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Call soap
     *
     * @throws SoapException if call failed
     */
    public function call()
    {
        try {
            // Set timeout
            if(isset($this->options['timeout']) && is_numeric($this->options['timeout'])) {
                $currentTimeout = ini_get('default_socket_timeout');
                ini_set('default_socket_timeout', $this->options['timeout']);
            }

            // Create SOAP headers
            $this->makeHeaders();

            // Client SOAP
            $oSoapRequest = new \SoapVar($this->request->create(), XSD_ANYXML);
            if(isset($this->options['location'])) {
                $this->__setLocation($this->options['location']);
            }

            $this->response = $this->__soapCall(
                $this->request->getUri(),
                array($oSoapRequest),
                $this->options,
                $this->header
            );

            // Remise en place du timeout
            if($currentTimeout) {
                ini_set('default_socket_timeout', $currentTimeout);
            }

        }catch(\SoapFault $e) {
            $exception = new SoapException($e->faultcode, $e->getMessage());
            $exception->setTrame($this->__getLastRequest());

            throw $exception;
        }
    }

    /**
     * Get Response (headers and body)
     *
     * @return array|null
     */
    public function getResponse()
    {
        if($this->response) {
            return array(
                'headers' => $this->__getLastResponseHeaders(),
                'body'    => $this->__getLastResponse()
            );
        }

        return null;
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
     * Create Soap headers
     */
    protected function makeHeaders()
    {
        $headers = $this->request->getHeaders();
        $namespace = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd";
        if(isset($headers['namespace'])) {
            $namespace = $headers['namespace'];
        }
        $sMust = '';
        if(isset($headers['mustunderstand']) && $headers['mustunderstand']) {
            $sMust = 'SOAP-ENV:mustUnderstand="1"';
        }
        if($headers['login'] != '' && $headers['password'] != '') {

            $sHeader = '<wsse:Security '.$sMust.' xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                                <wsse:UsernameToken wsu:Id="UsernameToken-6868426" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                                        <wsse:Username>'.$headers['login'].'</wsse:Username>
                                        <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$headers['password'].'</wsse:Password>
                                </wsse:UsernameToken>
                        </wsse:Security>';
            if($this->options['signature']) {
                $sHeader .='<Signature xmlns="http://www.canal-plus.com/signature">'.$headers['signature'].'</Signature>';
            }
            $authVar = new \SoapVar($sHeader, XSD_ANYXML, NULL, $namespace);

            $this->header = new \SoapHeader($namespace, "Security", $authVar);
        }
    }

    /**
     * Multiple namespaces in SOAP Request
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
            $countNs = 2; // start with third ns

            if(is_array($namespaces)&& !empty($namespaces)){
                foreach($namespaces as $namespace){
                    $countNs++;
                    $request = str_replace( '><SOAP-ENV:Header>',' xmlns:ns'.$countNs.'="'.$namespace.'"><SOAP-ENV:Header>' , $request);
                }
            }
            $this->requestOverride = $request;
        }
        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }

    /**
     * Last request
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