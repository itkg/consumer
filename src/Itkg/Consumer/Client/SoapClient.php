<?php

namespace Itkg\Consumer\Client;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SoapClient
 *
 * Soap client based on \SoapClient
 *
 * @package Itkg\Consumer\Client
 */
class SoapClient extends \SoapClient implements ClientInterface
{
    const HEADER_SECURITY = '<wsse:Security %s xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <wsse:UsernameToken wsu:Id="UsernameToken-6868426" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
            <wsse:Username>%s</wsse:Username>
            <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">%s</wsse:Password>
        </wsse:UsernameToken>
    </wsse:Security>';

    const HEADER_SIGNATURE = '<Signature xmlns="%s">%s</Signature>';

    /**
     * @var string
     */
    private $overridenRequest;
    /**
     * @var array
     */
    private $options = array();

    /**
     * @var string
     */
    private $headerLogin;

    /**
     * @var string
     */
    private $headerPassword;

    /**
     * @param string $wsdl
     * @param array  $options
     */
    public function __construct($wsdl, array $options = array())
    {
        $this->configure($options);

        parent::__construct($wsdl, $this->options);
    }

    /**
     * Send soap request & set Response content
     * @param Request $request
     * @param Response $response
     */
    public function sendRequest(Request $request, Response $response)
    {
        $response->setContent(
            $this->__soapCall(
                $request->getUri(),
                array(
                    $this->getClientRequest($request)
                ),
                $this->options,
                $this->getHeader()
            )
        );
    }

    /**
     * Get security infos from options
     */
    protected function configureSecurity()
    {
        if(isset($this->options['login']) && isset($this->options['password'])) {
            $this->headerLogin = $this->options['login'];
            $this->headerPassword = $this->options['password'];
            unset($this->options['login'], $this->options['password']);
        }

        if (isset($this->options['http_auth_login']) && isset($this->options['http_auth_password'])) {
            $this->options['login'] = $this->options['http_auth_login'];
            $this->options['password'] = $this->options['http_auth_password'];
        }
    }

    /**
     * Allow multiple soap namespaces
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
        if (isset($this->options['namespaces']) && is_array($this->options['namespaces'])) {
            foreach($this->options['namespaces'] as $index => $namespace){
                $request = str_replace(
                    ' ><SOAP-ENV:Header>',
                    sprintf(
                        ' xmlns:ns%s="%s"><SOAP-ENV:Header>',
                        $index,
                        $namespace
                    ),
                    $request
                );
            }
            $this->overridenRequest = $request;
        }
        return parent::__doRequest($request, $location, $action, $version, $one_way = 0);
    }

    /**
     * Get last request
     *
     * @return string
     */
    public function __getLastRequest()
    {
        if(null !== $this->overridenRequest){
            return $this->overridenRequest;
        }

        return parent::__getLastRequest();

    }

    /**
     * configure options
     *
     * @param array $options
     */
    protected function configure(array $options = array())
    {
        $this->options = array(
            'connection_timeout' => 2,
            'trace'              => false,
            'encoding'           => 'UTF8',
            'soap_version'       => SOAP_1_1,
            'features'           => SOAP_SINGLE_ELEMENT_ARRAYS,
            'namespace'          => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd'
        );

        $this->options = array_merge($this->options, $options);

        if(isset($this->options['location'])) {
            $this->__setLocation($this->options['location']);
        }

        $this->configureSecurity();
    }

    /**
     * Soap Request
     *
     * @param Request $request
     *
     * @return \SoapVar
     */
    protected function getClientRequest(Request $request)
    {
        return new \SoapVar($request->getContent(), XSD_ANYXML);
    }

    /**
     * Get header if request is secured by login / password else null
     *
     * @return null|\SoapHeader
     */
    protected function getHeader()
    {
        if (!$this->isSecure()) {
            return null;
        }

        return new \SoapHeader(
            $this->options['namespace'],
            'Security',
            new \SoapVar(
                $this->createHeader(),
                XSD_ANYXML,
                NULL,
                $this->options['namespace']
            )
        );
    }

    /**
     * Check if headerLogin & headerPassword are set
     *
     * @return bool
     */
    protected function isSecure()
    {
        return $this->headerLogin && $this->headerPassword;
    }

    /**
     * create header string
     *
     * @return string
     */
    protected function createHeader()
    {
        $mustUnderstand = '';
        if (isset($this->options['must_understand']) && $this->options['must_understand']) {
            $mustUnderstand = 'SOAP-ENV:mustUnderstand="1"';
        }

        $header = sprintf(
            self::HEADER_SECURITY,
            $mustUnderstand,
            $this->headerLogin,
            $this->headerPassword
        );

        if (isset($this->options['signature']) && isset($this->options['signature_namespace'])) {
            $header .= sprintf(
                self::HEADER_SIGNATURE,
                $this->options['signature_namespace'],
                $this->options['signature']
            );
        }

        return $header;
    }
}
