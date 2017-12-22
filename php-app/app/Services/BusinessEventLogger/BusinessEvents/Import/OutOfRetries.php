<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvent;
use Psr\Log\LogLevel;

/**
 * Business Event that records we retried and ran out of retries.
 */
class OutOfRetries implements BusinessEvent
{
    use ImportBusinessEvent;

    const RETRY_EXCEEDED_EVENT_MESSAGE = 'Import Failed, out of retries';

    /**
     * OutOfRetries constructor.
     *
     * @param string     $aliasName
     * @param \Exception $exception
     * @param string     $retriesLeft
     * @param string     $totalRetries
     */
    public function __construct($aliasName, \Exception $exception, $retriesLeft, $totalRetries)
    {
        $this->context = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::EXCEPTION_FIELD      => $exception,
            IndexOperationEnum::ALIAS_FIELD          => $aliasName,
            IndexOperationEnum::RETRIES_LEFT_FIELD   => $retriesLeft,
            IndexOperationEnum::TOTAL_RETRIES        => $totalRetries,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => self::RETRY_EXCEEDED_EVENT_MESSAGE,
        ];

        $this->level = LogLevel::ERROR;
    }
}
