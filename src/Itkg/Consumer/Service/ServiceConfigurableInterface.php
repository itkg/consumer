<?php

namespace Itkg\Consumer\Service;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Interface ServiceConfigurableInterface
 *
 * Service configurable contract
 *
 * @package Itkg\Consumer\Service
 */
interface ServiceConfigurableInterface
{
    /**
     * Configure service options
     *
     * @param array $options
     * @param OptionsResolver $resolver
     *
     * @return $this
     */
    public function configure(array $options = array(), OptionsResolver $resolver = null);

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions();

    /**
     * Get option by key
     *
     * @param $key
     *
     * @return mixed
     */
    public function getOption($key);

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasOption($key);
}
