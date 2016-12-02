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
     * {@inheritDoc}
     */
    public function processAll($maxExecutionTime = 3600, \Closure $logCallback = null)
    {
        $initialTime = time();
        $this->log($logCallback, sprintf('Process start, Max execution time : defined %s secondes', $maxExecutionTime));
        $this->log($logCallback, sprintf(
            ' %s keys to refresh and %s keys locked',
            count($this->reader->getAllItemsToRefresh()),
            count($this->reader->getAllItemsLocked())
        ));

        while(null !== $key = $this->reader->getFirstItem()) {
            try {
                $cachedService = $this->cacheAdapter->get($this->createCacheItem($key));
                $this->writer->replaceItem($key, WarmupQueue::STATUS_LOCKED);
                if ($cachedService instanceof AbstractService) {
                    $service = $this->services->getServiceByIdentifier($cachedService->getIdentifier());
                    $this->cacheWarmer->warm($service, $cachedService);
                        $this->log($logCallback, sprintf('SUCCESS ON REFRESH KEY %s', $key));
                }
            } catch(\Exception $e) {
                $this->log($logCallback, sprintf('ERROR ON REFRESH KEY %s : %s', $key, $e->getMessage()));
            }
            $this->writer->removeItem($key);
            if (time() - $initialTime > $maxExecutionTime) {
                $this->log($logCallback, sprintf('Max execution time of %s seconds reached. Terminating process', $maxExecutionTime));
                break;
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
