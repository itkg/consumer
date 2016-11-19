<?php

namespace Demo;

use Itkg\Consumer\Service\Service;
use Monolog\Formatter\FormatterInterface;

/**
 * Class MyFormatter
 */
class MyFormatter implements FormatterInterface
{
    const LOG_FORMAT = "%s - %s - %s - %s \n";
    /**
     * Formats a log record.
     *
     * @param  array $record A record to format
     *
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        /** @var Service $service */
        $service = $record['context']['service'];

        return sprintf(
            self::LOG_FORMAT,
            $record['datetime']->format('Y-m-d H:i:s'),
            $service->getRequest()->getUri(),
            $service->getRequest()->getClientIp(),
            $record['message']
        );
    }

    /**
     * Formats a set of log records.
     *
     * @param  array $records A set of records to format
     *
     * @return mixed The formatted set of records
     */
    public function formatBatch(array $records)
    {
        // TODO: Implement formatBatch() method.
    }
}
