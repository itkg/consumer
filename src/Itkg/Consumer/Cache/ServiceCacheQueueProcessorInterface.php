<?php

namespace Itkg\Consumer\Cache;

/**
 * interface ServiceCacheQueueProcessorInterface
 */
interface ServiceCacheQueueProcessorInterface
{
    /**
     * @param callable $logCallback
     */
    public function processAll(\Closure $logCallback);
}
