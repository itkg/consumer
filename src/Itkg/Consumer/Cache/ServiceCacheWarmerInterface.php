<?php

namespace Itkg\Consumer\Cache;

use Itkg\Consumer\Service\AbstractService;

/**
 * interface ServiceCacheWarmerInterface
 */
interface ServiceCacheWarmerInterface
{
    /**
     * @param AbstractService $service
     * @param AbstractService $cachedService
     */
    public function warm(AbstractService $service, AbstractService $cachedService);
}
