<?php

namespace Itkg\Consumer\Service;

use Itkg\Core\CacheableInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CacheableService
 *
 * Light service with cacheable functionality
 *
 * @package Itkg\Consumer\Service
 */
class CacheableService extends LightService implements CacheableInterface
{
    /**
     * @var bool
     */
    private $loaded = false;

    /**
     * Hash key getter
     *
     * @return string
     */
    public function getHashKey()
    {
        return strtr($this->getIdentifier(), ' ','_').md5(
            sprintf(
                '%s_%s_%s',
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
        return call_user_func(
            $this->options['cache_serializer'],
            $this->response
        );
    }

    /**
     * Restore data after cache load
     *
     * @param $data
     * @return $this
     */
    public function setDataFromCache($data)
    {
        $this->response = call_user_func(
            $this->options['cache_unserializer'],
            $data
        );
    }

    /**
     * @param array $options
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    protected function configure(array $options = array(), OptionsResolver $resolver = null)
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults(array(
            'cache_ttl'         => null,
            'cache_serializer'  => 'serialize',
            'cache_unserializer' => 'unserialize'
        ));

        parent::configure($options, $resolver);
    }
}
