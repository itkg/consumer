<?php

namespace Itkg\Consumer\Cache;

use Itkg\Core\Cache\AdapterInterface;
use Itkg\Core\Cache\CacheableData;
use Itkg\Core\CacheableInterface;

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
     * {@inheritDoc}
     */
    public function getFirstItem($status = WarmupQueue::STATUS_REFRESH)
    {
        $keys = $this->cacheAdapter->get($this->createCacheItem());

        if (!is_array($keys)) {
            return null;
        }
        // if no specific status we return first KEY
        if(null === $status ) {
            return array_pop(array_keys($keys));
        }
        // return the first key that match status
        foreach ($keys as $key => $content) {
            if ($status === $content['status']) {
                return $key;
            }
        }
        
        //no key match the given status then return null
        return null;
    }

    /**
     * @return CacheableData
     */
    private function createCacheItem()
    {
        return new CacheableData($this->cacheKey, null, array());
    }

    /**
     * @return array
     */
    public function getAllItemsToRefresh()
    {
        $keys = $this->cacheAdapter->get($this->createCacheItem());

        return array_filter($keys, function($content) {
            return $content['status'] == WarmupQueue::STATUS_REFRESH;
        });
    }

    /**
     * @return array
     */
    public function getAllItemsLocked()
    {
        $keys = $this->cacheAdapter->get($this->createCacheItem());

        return array_filter($keys, function($content) {
            return $content['status'] == WarmupQueue::STATUS_LOCKED;
        });
    }

    /**
     * @param CacheableInterface $item
     *
     * @return bool
     */
    public function isItemLocked(CacheableInterface $item)
    {
        $keys = $this->cacheAdapter->get($this->createCacheItem());

        foreach ($keys as $key => $content) {
            if ($key === $item->getHashKey()) {
                return WarmupQueue::STATUS_LOCKED == $content['status'];
            }
        }

        return false;
    }
}
