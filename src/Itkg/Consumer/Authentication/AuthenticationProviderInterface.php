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
     * @return string
     */
    public function getToken();

    /**
     * Inject authenticated information into service components
     *
     * @param ServiceInterface $service
     */
    public function hydrate(ServiceInterface $service);
}
