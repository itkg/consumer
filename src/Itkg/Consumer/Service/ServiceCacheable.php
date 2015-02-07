<?php

namespace Itkg\Consumer\Service;

use Itkg\Core\CacheableInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ServiceCacheable
 *
 * Light service with cacheable functionality
 *
 * @package Itkg\Consumer\Service
 */
class ServiceCacheable extends LightService implements CacheableInterface
{
    /**
     * @var bool
     */
    private $loaded;

    /**
     * @param array $options
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configure(array $options = array(), OptionsResolver $resolver = null)
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults(array(
            'cache_ttl' => null
        ));

        parent::configure($options, $resolver);
    }

    /**
     * Hash key getter
     *
     * @return string
     */
    public function getHashKey()
    {
        return md5(
            sprintf(
                '%s_%s_%s_%s',
                $this->getIdentifier(),
                $this->request->getContent(),
                $this->request->getUri(),
                json_encode($this->request->headers->all())
            )
        );
    }

    /**
     * Get cache TTL
     *
     * @return int
     */
    public function getTtl()
    {
        return $this->options['cache_ttl'];
    }

    /**
     * Return if object is already loaded from cache
     *
     * @return bool
     */
    public function isLoaded()
    {
        return $this->loaded;
    }

    /**
     * Set is loaded
     *
     * @param bool $isLoaded
     */
    public function setIsLoaded($isLoaded = true)
    {
        $this->loaded = $isLoaded;
    }

    /**
     * Get data from service for cache set
     *
     * @return mixed
     */
    public function getDataForCache()
    {
        return serialize($this->response);
    }

    /**
     * Restore data after cache load
     *
     * @param $data
     * @return $this
     */
    public function setDataFromCache($data)
    {
        $this->response = unserialize($data);
    }
}
