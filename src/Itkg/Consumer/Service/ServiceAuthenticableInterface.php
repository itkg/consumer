<?php

namespace Itkg\Consumer\Service;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface ServiceAuthenticableInterface
 *
 * @package Itkg\Consumer\Service
 */
interface ServiceAuthenticableInterface
{
    /**
     * Authenticate service
     *
     * @return mixed
     */
    public function authenticate();

    /**
     * Service is authenticated or not
     *
     * @return bool
     */
    public function isAuthenticated();

    /**
     * Inject authenticated data into the request / client
     */
    public function makeAuthenticated();
}
