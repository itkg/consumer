<?php

namespace Itkg\Consumer\Client;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SoapClient extends \SoapClient implements ClientInterface
{
    /**
     * @var array
     */
    private $options = array();

    /**
     * @param string $wsdl
     * @param array  $options
     */
    public function __construct($wsdl, array $options = array())
    {
        $this->options = array_merge(
            array(
                'connection_timeout' => 2,
                'trace'              => true,
                'encoding'           => 'UTF8',
                'soap_version'       => SOAP_1_1,
                'features'           => SOAP_SINGLE_ELEMENT_ARRAYS,
            ),
            $options
        );

        parent::__construct($wsdl, $this->options);
    }

    /**
     * Send soap request & set Response content
     * @param Request $request
     * @param Response $response
     */
    public function sendRequest(Request $request, Response $response)
    {
        /**
         * @TODO : Manage soap headers
         */
        $response->setContent(
            $this->__soapCall(
                $request->getMethod(),
                array(
                    $request->getContent()
                ),
                $this->options
            )
        );
    }
}
