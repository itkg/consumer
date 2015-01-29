<?php

namespace Itkg\Consumer\Service;

use Psr\Log\LoggerInterface;

interface ServiceLoggableInterface
{
    /**
     * @return LoggerInterface
     */
    public function getLogger();

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger);
}
