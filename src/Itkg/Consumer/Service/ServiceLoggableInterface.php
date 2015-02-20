<?php

namespace Itkg\Consumer\Service;

/**
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
interface ServiceLoggableInterface
{
    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger();
}
