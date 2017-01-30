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
        foreach ($keys as $key => $content) {
            if ($status === $content['status']) {
                return $key;
            }
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
