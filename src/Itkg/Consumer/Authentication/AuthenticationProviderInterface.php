<?php

namespace Itkg\Consumer\Authentication;

use Itkg\Consumer\Service\ServiceInterface;

/**
 * Interface AuthenticationProviderInterface
 *
 * For specific authentication before service call
 *
 * @package Itkg\Consumer\Authentication
 */
interface AuthenticationProviderInterface
{
    /**
     * Authenticate service
     */
    public function authenticate();

    /**
     * Configure provider options
     *
     * @param array $options
     *
     * @return $this
     */
    public function configure(array $options);

    /**
     * Get access token needed for service authentication
     *
     * @return string
     */
    public function getToken();

    /**
     * Inject authenticated information into service components (client, request, etc.)
     *
     * @param ServiceInterface $service
     */
    public function hydrate(ServiceInterface $service);
}
