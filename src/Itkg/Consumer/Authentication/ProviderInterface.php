<?php

namespace Itkg\Consumer\Authentication;

use Guzzle\Http\ClientInterface;

/**
 * Interface ProviderInterface
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
interface ProviderInterface
{
    public function getAuthToken();

    public function hydrate(ClientInterface $client);

    public function hasAccess();

    public function authenticate();
}