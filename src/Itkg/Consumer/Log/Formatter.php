<?php

namespace Itkg\Consumer\Log;


use Itkg\Consumer\Service\Events;
use Itkg\Log\AbstractFormatter;

class Formatter extends AbstractFormatter
{

    /**
     * Format
     *
     * @param string $log
     */
    public function format($log)
    {
        switch($this->params['action']) {
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

    protected function formatFromCache($log)
    {

    }

    protected function formatBindRequest($log)
    {

    }

    protected function formatBindResponse($log)
    {

    }

    protected function formatPreCall($log)
    {

    }

    protected function formatPostCall($log)
    {

    }

    protected function formatFailedCall($log)
    {

    }

    protected function formatSuccessCall($log)
    {

    }
}