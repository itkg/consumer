<?php

namespace Itkg\Consumer\Service;

use Itkg\Consumer\Client\ClientInterface;
use Itkg\Consumer\Request;
use Itkg\Core\ConfigInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;

class Service extends ServiceCacheable implements ServiceLoggableInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher
     * @param ConfigInterface $config
     * @param ClientInterface $client
     * @param \Psr\Log\LoggerInterface $logger
     * @param Request $request
     * @param Response $response
     */
    public function __construct(EventDispatcher $eventDispatcher, ConfigInterface $config, ClientInterface $client, LoggerInterface $logger, Request $request = null, Response $response = null)
    {
        parent::__construct($eventDispatcher, $config, $client, $request, $response);
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
