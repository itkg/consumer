<?php

namespace Itkg\Consumer\Client\Soap\Exception;

/**
 * Class SoapException
 *
 * @package Itkg\Consumer\Client\Soap\Exception
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class SoapException extends \SoapFault
{
    /**
     * A soap request
     *
     * @var string
     */
    protected $trame;
    /**
     * Request parameters
     *
     * @var array
     */
    protected $data;

    /**
     * Getter Request trame
     *
     * @return string
     */
    public function getTrame()
    {
        return $this->trame;
    }

    /**
     * Set trame
     *
     * @param string $trame A soap request
     */
    public function setTrame($trame)
    {
        $this->trame = $trame;
    }

    /**
     * Getter data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set data
     *
     * @param array $data List of data
     */
    public function setDatas($data)
    {
        $this->data = $data;
    }
}