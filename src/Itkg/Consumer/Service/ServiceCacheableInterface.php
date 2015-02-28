<?php

namespace Itkg\Consumer\Service;

use Itkg\Core\Cache\AdapterInterface;
use Itkg\Core\CacheableInterface;

interface ServiceCacheableInterface extends CacheableInterface
{
    /**
     * @return AdapterInterface
     */
    public function getCacheAdapter();
} 