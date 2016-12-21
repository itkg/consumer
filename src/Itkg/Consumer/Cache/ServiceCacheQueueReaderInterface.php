<?php

namespace Itkg\Consumer\Cache;

use Itkg\Core\Cache\CacheableData;
use Itkg\Core\CacheableInterface;

/**
 * interface ServiceCacheQueueReaderInterface
 */
interface ServiceCacheQueueReaderInterface
{
    /**
     * @param string $status
     *
     * @return mixed
     */
    public function getFirstItem($status = WarmupQueue::STATUS_REFRESH);

    /**
     * @return array
     */
    public function getAllItemsToRefresh();

    /**
     * @return array
     */
    public function getAllItemsLocked();

    /**
     * @param CacheableInterface $item
     *
     * @return bool
     */
    public function isItemLocked(CacheableInterface $item);
}
