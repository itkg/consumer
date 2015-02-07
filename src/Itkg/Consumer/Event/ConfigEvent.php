<?php

namespace Itkg\Consumer\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ConfigEvent
 * 
 * @package Itkg\Consumer\Event
 */
class ConfigEvent extends Event
{
    /**
     * @var array
     */
    private $options = array();

    /**
     * @var \Symfony\Component\OptionsResolver\OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param OptionsResolver $optionsResolver
     * @param array $options
     */
    public function __construct(OptionsResolver $optionsResolver, array $options)
    {
        $this->options = $options;
        $this->optionsResolver = $optionsResolver;
    }

    /**
     * Add an option (or replace existing option)
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function addOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set all options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return OptionsResolver
     */
    public function getOptionsResolver()
    {
        return $this->optionsResolver;
    }
}
