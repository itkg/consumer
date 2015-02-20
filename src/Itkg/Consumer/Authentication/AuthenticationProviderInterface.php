<?php

namespace Itkg\Consumer\Authentication;

use Symfony\Component\HttpFoundation\Request;

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
     * Inject authenticated information into request
     *
     * @param Request $request
     */
    public function hydrateRequest(Request $request);
}
