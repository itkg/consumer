<?php

namespace Itkg\Consumer\Service;

/**
 * Interface ServiceInterface
 *
 * Advanced service contract
 *
 * @package Itkg\Consumer\Service
 */
interface AdvancedServiceInterface extends ServiceInterface
{
    /**
     * @return bool
     */
    public function isDisabled();
}
