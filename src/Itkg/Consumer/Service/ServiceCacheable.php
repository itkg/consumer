<?php

namespace Itkg\Consumer\Service;

use Itkg\Core\CacheableInterface;

class ServiceCacheable extends LightService implements CacheableInterface
{
    /**
     * @var bool
     */
    private $loaded;

    /**
     * Hash key getter
     *
     * @return string
     */
    public function getHashKey()
    {
        return '';
    }

    /**
     * Get cache TTL
     *
     * @return int
     */
    public function getTtl()
    {
        return $this->config->has('cache_ttl') ? $this->config->get('cache_ttl') : null;
    }

    /**
     * Return if object is already loaded from cache
     *
     * @return bool
     */
    public function isLoaded()
    {
        return $this->loaded;
    }

    /**
     * Set is loaded
     *
     * @param bool $isLoaded
     */
    public function setIsLoaded($isLoaded = true)
    {
        $this->loaded = $isLoaded;
    }

    /**
     * Get data from entity for cache set
     *
     * @return mixed
     */
    public function getDataForCache()
    {
        // TODO: Implement getDataForCache() method.
    }

    /**
     * @param $data
     * @return $this
     */
    public function setDataFromCache($data)
    {
        // TODO: Implement setDataFromCache() method.
    }
}