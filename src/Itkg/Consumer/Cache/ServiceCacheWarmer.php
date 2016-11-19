<?php

namespace Itkg\Consumer\Cache;

use Itkg\Consumer\Service\AbstractService;
use Itkg\Consumer\Service\ServiceCacheableInterface;

/**
 * class ServiceCacheWarmer 
 */
class ServiceCacheWarmer implements ServiceCacheWarmerInterface
{
    /**
     * {@inheritDoc}
     */
    public function warm(AbstractService $service, AbstractService $cachedService)
    {
        if (!$service instanceof ServiceCacheableInterface) {
            return;
        }

        $service->setNoCache(true);
        $service->getClient()->setOptions($cachedService->getClient()->getOptions());
        $service->sendRequest($cachedService->getRequest());
    }
}
