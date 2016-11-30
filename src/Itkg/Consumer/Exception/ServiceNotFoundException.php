<?php

namespace Itkg\Consumer\Exception;

/**
 * Class ServiceNotFoundException
 */
class ServiceNotFoundException extends \Exception
{
    /**
     * @param string $identifier
     */
    public function __construct($identifier)
    {
        parent::__construct(
            sprintf('Service %s not found', $identifier)
        );
    }
}
