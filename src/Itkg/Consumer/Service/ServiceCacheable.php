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
        return md5(sprintf('%s_%s_%s',
            $this->request->getContent(),
            $this->request->getUri(),
            json_encode($this->request->headers->all())
        ));
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
     * Get data from service for cache set
     *
     * @return mixed
     */
    public function getDataForCache()
    {
        return serialize($this->response);
    }

    /**
     * Restore data after cache load
     *
     * @param $data
     * @return $this
     */
    public function setDataFromCache($data)
    {
        $this->response = unserialize($data);
    }
}