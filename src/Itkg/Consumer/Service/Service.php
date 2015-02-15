<?php

namespace Itkg\Consumer\Service;

use Itkg\Consumer\Client\ClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Itkg\Consumer\Response;

/**
 * Class Service
 *
 * A full service with cacheable & loggable functionnality
 *
 * @package Itkg\Consumer\Service
 */
class Service extends CacheableService implements ServiceLoggableInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher
     * @param ClientInterface $client
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $options
     */
    public function __construct(
        EventDispatcher $eventDispatcher,
        ClientInterface $client,
        LoggerInterface $logger,
        array $options = array())
    {
        $this->logger = $logger;

        parent::__construct($eventDispatcher, $client, $options);
    }

    /**
     * array LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }
}
