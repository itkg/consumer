<?php

namespace Itkg\Consumer\Cache;

use Itkg\Consumer\Service\AbstractService;
use Itkg\Consumer\Service\ServiceCollection;
use Itkg\Core\Cache\AdapterInterface;
use Itkg\Core\Cache\CacheableData;

/**
 * class ServiceCacheQueueProcessor 
 */
class ServiceCacheQueueProcessor implements ServiceCacheQueueProcessorInterface
{
    /**
     * @var ServiceCacheQueueReaderInterface
     */
    private $reader;

    /**
     * @var ServiceCacheQueueWriterInterface
     */
    private $writer;

    /**
     * @var AdapterInterface
     */
    private $cacheAdapter;

    /**
     * @var ServiceCacheWarmer
     */
    private $cacheWarmer;

    /**
     * @var ServiceCollection
     */
    private $services;

    /**
     * @param ServiceCacheQueueReaderInterface $reader
     * @param ServiceCacheQueueWriterInterface $writer
     * @param ServiceCacheWarmer               $cacheWarmer
     * @param AdapterInterface                 $adapter
     */
    public function __construct(
        ServiceCacheQueueReaderInterface $reader,
        ServiceCacheQueueWriterInterface $writer,
        ServiceCacheWarmer $cacheWarmer,
        AdapterInterface $adapter,
        ServiceCollection $services
    ) {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->cacheWarmer = $cacheWarmer;
        $this->cacheAdapter = $adapter;
        $this->services = $services;
    }

    /**
     * @param callable $logCallback
     */
    public function processAll(\Closure $logCallback = null)
    {
        while(null !== $key = $this->reader->getFirstItem()) {
            try {
                $cachedService = $this->cacheAdapter->get($this->createCacheItem($key));
                $this->writer->removeItem($key);
                if ($cachedService instanceof AbstractService) {
                    $service = $this->services->getServiceByIdentifier($cachedService->getIdentifier());
                    $this->cacheWarmer->warm($service, $cachedService);
                        $this->log($logCallback, sprintf('SUCCESS ON REFRESH KEY %s', $key));
                }
            } catch(\Exception $e) {
                $this->log($logCallback, sprintf('ERROR ON REFRESH KEY %s : %s', $key, $e->getMessage()));
            }
        }
    }

    /**
     * @param string $key
     *
     * @return CacheableData
     */
    private function createCacheItem($key)
    {
        return new CacheableData($key, null, array());
    }

    /**
     * @param callable $logCallback
     * @param string   $message
     */
    private function log(\Closure $logCallback = null, $message)
    {
        if ($logCallback) {
            $logCallback($message);
        }
    }
}
