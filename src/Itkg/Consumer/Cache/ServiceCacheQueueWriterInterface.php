<?php

namespace Itkg\Consumer\Cache;

/**
 * interface ServiceCacheQueueWriterInterface
 */
interface ServiceCacheQueueWriterInterface
{
    /**
     * @param string $key
     * @param mixed $value
     */
    public function addItem($key, $value);

    /**
     * @param string $key
     */
    public function removeItem($key);

    /**
     * @param stirng $key
     * @param mixed $value
     */
    public function replaceItem($key, $value);
}
