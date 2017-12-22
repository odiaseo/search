<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvent;
use Psr\Log\LogLevel;

/**
 * Logs a business event that a retry occurred.
 */
class RetryOccurred implements BusinessEvent
{
    use ImportBusinessEvent;

    const RETRY_EVENT_MESSAGE = 'Import failed, retrying';

    /**
     * RetryOccurred constructor.
     *
     * @param string     $aliasName
     * @param \Exception $exception
     * @param string     $retriesLeft
     * @param string     $totalRetries
     * @param string     $waitTime
     */
    public function __construct($aliasName, \Exception $exception, $retriesLeft, $totalRetries, $waitTime)
    {
        $this->context = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::EXCEPTION_FIELD      => $exception,
            IndexOperationEnum::ALIAS_FIELD          => $aliasName,
            IndexOperationEnum::RETRIES_LEFT_FIELD   => $retriesLeft,
            IndexOperationEnum::TOTAL_RETRIES        => $totalRetries,
            IndexOperationEnum::SECONDS_TO_RETRY     => $waitTime,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => self::RETRY_EVENT_MESSAGE,
        ];
        $this->level = LogLevel::WARNING;
    }
}
