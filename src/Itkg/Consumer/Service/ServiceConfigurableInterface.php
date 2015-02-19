<?php

namespace Itkg\Consumer\Service;

interface ServiceConfigurableInterface
{
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
