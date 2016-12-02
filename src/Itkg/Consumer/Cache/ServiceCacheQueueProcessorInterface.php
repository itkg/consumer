<?php

namespace Itkg\Consumer\Cache;

/**
 * interface ServiceCacheQueueProcessorInterface
 */
interface ServiceCacheQueueProcessorInterface
{
    /**
     * @param int      $maxExecutionTime
     * @param callable $logCallback
     */
    public function processAll($maxExecutionTime = 3600, \Closure $logCallback);
}
