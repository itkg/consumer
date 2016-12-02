<?php

namespace Itkg\Consumer\Cache;

use Itkg\Core\Cache\AdapterInterface;
use Itkg\Core\Cache\CacheableData;

/**
 * class ServiceCacheQueueWriter 
 */
class ServiceCacheQueueWriter implements ServiceCacheQueueWriterInterface
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
     * @param string $key
     * @param mixed  $value
     */
    public function addItem($key, $value)
    {
        $values = $this->cacheAdapter->get($this->createCacheItem());
        if (!isset($values[$key])) { // Avoid to replace existing keys
            $values[$key] = $value;

            $this->cacheAdapter->set($this->createCacheItem($values));
        }
    }

    /**
     * @param string $key
     */
    public function removeItem($key)
    {
        $values = $this->cacheAdapter->get($this->createCacheItem());

        if (!isset($values[$key])) {
            return;
        }

        unset($values[$key]);
        $this->cacheAdapter->set($this->createCacheItem($values));
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function replaceItem($key, $value)
    {
        $values = $this->cacheAdapter->get($this->createCacheItem());
        $values[$key] = $value;

        $this->cacheAdapter->set($this->createCacheItem($values));
    }

    /**
     * @param array $values
     *
     * @return CacheableData
     */
    private function createCacheItem(array $values = array())
    {
        return new CacheableData($this->cacheKey, null, $values);
    }
}
