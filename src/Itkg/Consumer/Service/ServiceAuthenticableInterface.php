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
     * Inject autenticated data into the request
     *
     * @param Request $request
     */
    public function makeRequestAuthenticated();
}
