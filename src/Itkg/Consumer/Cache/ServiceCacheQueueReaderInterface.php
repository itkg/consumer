<?php

namespace Itkg\Consumer\Cache;

/**
 * interface ServiceCacheQueueReaderInterface
 */
interface ServiceCacheQueueReaderInterface
{
    /**
     * @return mixed
     */
    public function getFirstItem();
}
