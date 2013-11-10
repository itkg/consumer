<?php

namespace Itkg\Consumer\Log;


use Itkg\Consumer\Service\Events;
use Itkg\Log\AbstractFormatter;

/**
 * Class Formatter
 *
 * @package Itkg\Consumer\Log
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class Formatter extends AbstractFormatter
{

    /**
     * Format a log
     *
     * @param string $log A log to format
     */
    public function format($log)
    {
        switch ($this->params['action']) {
            case Events::BIND_REQUEST :
                $this->formatBindRequest($log);
                break;
            case Events::BIND_RESPONSE :
                $this->formatBindResponse($log);
                break;
            case Events::PRE_CALL :
                $this->formatPreCall($log);
                break;
            case Events::POST_CALL :
                $this->formatPostCall($log);
                break;
            case Events::FAIL_CALL :
                $this->formatFailedCall($log);
                break;
            case Events::SUCCESS_CALL :
                $this->formatSuccessCall($log);
                break;
            case Events::FROM_CACHE :
                $this->formatFromCache($log);
                break;
        }
    }

    /**
     * Format from cache log
     *
     * @param string $log A log to format
     */
    protected function formatFromCache($log)
    {

    }

    /**
     * Format bind request log
     *
     * @param string $log A log to format
     */
    protected function formatBindRequest($log)
    {

    }

    /**
     * Format bind response log
     *
     * @param string $log A log to format
     */
    protected function formatBindResponse($log)
    {

    }

    /**
     * Format pre call log
     *
     * @param string $log A log to format
     */
    protected function formatPreCall($log)
    {

    }

    /**
     * Format post call log
     *
     * @param string $log A log to format
     */
    protected function formatPostCall($log)
    {

    }

    /**
     * Format failed call log
     *
     * @param string $log A log to format
     */
    protected function formatFailedCall($log)
    {

    }

    /**
     * Format success call log
     *
     * @param string $log A log to format
     */
    protected function formatSuccessCall($log)
    {

    }
}