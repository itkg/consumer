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
    /**
     * Getter auth token
     *
     * @return mixed
     */
    public function getAuthToken();

    /**
     * Hydrate client with authentication parameters
     *
     * @param $client
     * @return mixed
     */
    public function hydrateClient($client);

    /**
     * Has access or need authentication?
     *
     * @return mixed
     */
    public function hasAccess();

    /**
     * Authenticate process
     *
     * @return mixed
     */
    public function authenticate();

    /**
     * Clean provider parameters
     *
     * @return mixed
     */
    public function clean();

    /**
     * Merge data or parameters into provider
     *
     * @param array $data
     * @return mixed
     */
    public function mergeData(array $data = array());
}