<?php

namespace Itkg\Consumer\Cache;

use Itkg\Core\Cache\AdapterInterface;
use Itkg\Core\Cache\CacheableData;

/**
 * class ServiceCacheQueueReader 
 */
class ServiceCacheQueueReader implements ServiceCacheQueueReaderInterface
{
    /**
     * @var AdapterInterface $cacheAdapter
     */
    private $cacheAdapter;

    /**
     * @var string
     */
    private $cacheKey;

    /**
     * @param AdapterInterface $cacheAdapter
     * @param string           $cacheKey
     */
    public function __construct(AdapterInterface $cacheAdapter, $cacheKey = WarmupQueue::KEY_NAME)
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->cacheKey = $cacheKey;
    }

    /**
     * @return mixed
     */
    public function getFirstItem()
    {
        $keys = $this->cacheAdapter->get($this->createCacheItem());

        if (!is_array($keys)) {
            return null;
        }

        return array_pop($keys);
    }

    /**
     * @return CacheableData
     */
    private function createCacheItem()
    {
        return new CacheableData($this->cacheKey, null, array());
    }
}
