<?php

namespace Itkg\Consumer\Service;

use Itkg\Core\Cache\AdapterInterface;
use Itkg\Core\CacheableInterface;

interface ServiceCacheableInterface
{
    /**
     * @return AdapterInterface
     */
    public function getCacheAdapter();

    /**
     * Hash key getter
     *
     * @return string
     */
    public function getHashKey();

    /**
     * Get cache TTL
     *
     * @return int
     */
    public function getTtl();

    /**
     * Get cache TTL (considered as fresh)
     *
     * @return int
     */
    public function getFreshTtl();

    /**
     * Return if object is already loaded from cache
     *
     * @return bool
     */
    public function isLoaded();

    /**
     * Set is loaded
     *
     * @param bool $isLoaded
     */
    public function setIsLoaded($isLoaded = true);

    /**
     * @return bool
     */
    public function canBeWarmed();

    /**
     * @return bool
     */
    public function isObsolete();

    /**
     * @param bool $noCache
     *
     * @return $this
     */
    public function setNoCache($noCache = true);

    /**
     * @return bool
     */
    public function isNoCache();
}
