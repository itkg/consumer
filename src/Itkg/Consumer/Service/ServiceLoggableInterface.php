<?php

namespace Itkg\Consumer\Service;

/**
 * interface ServiceLoggableInterface
 *
 * Loggable service conctract
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
interface ServiceLoggableInterface
{
    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger();
}
