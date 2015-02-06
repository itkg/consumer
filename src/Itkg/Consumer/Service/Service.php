<?php

namespace Itkg\Consumer\Service;

use Itkg\Consumer\Client\ClientInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @param ClientInterface $client
     * @param \Psr\Log\LoggerInterface $logger
     * @param Request $request
     * @param Response $response
     * @param array $config
     */
    public function __construct(EventDispatcher $eventDispatcher, ClientInterface $client, LoggerInterface $logger, Request $request = null, Response $response = null, array $config = array())
    {
        $this->logger = $logger;

        parent::__construct($eventDispatcher, $client, $request, $response, $config);
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
