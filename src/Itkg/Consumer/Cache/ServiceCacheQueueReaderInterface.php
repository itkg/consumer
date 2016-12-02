<?php

namespace Itkg\Consumer\Cache;

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
}
