<?php

namespace Itkg\Consumer;

/**
 * Class ClientInterface
 * @package Itkg\Consumer
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
interface ClientInterface
{
    /**
     * Init client with Request
     *
     * @param Request $request Request object
     * @return mixed
     */
    public function init(Request $request);

    /**
     * Get response values
     *
     * @return array
     */
    public function getResponse();
}