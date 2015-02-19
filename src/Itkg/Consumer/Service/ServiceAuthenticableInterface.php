<?php

namespace Itkg\Consumer\Service;

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
     * @param bool $authenticated
     * @return $this
     */
    public function setAuthenticated($authenticated);
}
