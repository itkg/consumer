<?php

namespace Itkg\Consumer\Event;

use Itkg\Consumer\Service\ServiceConfigurableInterface;
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
     * @var ServiceConfigurableInterface
     */
    private $service;
    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param OptionsResolver $optionsResolver
     * @param ServiceConfigurableInterface $service
     */
    public function __construct(OptionsResolver $optionsResolver, ServiceConfigurableInterface $service)
    {
        $this->service = $service;
        $this->optionsResolver = $optionsResolver;
    }

    /**
     * Get service
     *
     * @return ServiceConfigurableInterface
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Get options resolver
     *
     * @return OptionsResolver
     */
    public function getOptionsResolver()
    {
        return $this->optionsResolver;
    }
}
