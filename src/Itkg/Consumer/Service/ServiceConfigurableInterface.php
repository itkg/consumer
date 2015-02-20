<?php

namespace Itkg\Consumer\Service;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface ServiceConfigurableInterface
{
    /**
     * Configure service options
     *
     * @param array $options
     * @param OptionsResolver $resolver
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

    /**
     * Set all options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options);
}
