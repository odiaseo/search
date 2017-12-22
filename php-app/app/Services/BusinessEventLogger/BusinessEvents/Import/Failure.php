<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import;

use Exception;
use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvent;
use Psr\Log\LogLevel;

/**
 * Log that the import failed.
 */
class Failure implements BusinessEvent
{
    use ImportBusinessEvent;

    const IMPORT_FAILURE_EVENT_MESSAGE = 'Search index build failure, removing build index';

    /**
     * Success constructor.
     *
     * @param string    $indexName
     * @param string    $aliasName
     * @param Exception $exception
     * @param float     $operationTime
     */
    public function __construct($indexName, $aliasName, Exception $exception, $operationTime)
    {
        $this->context = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => self::IMPORT_FAILURE_EVENT_MESSAGE,
            IndexOperationEnum::INDEX_FIELD          => $indexName,
            IndexOperationEnum::ALIAS_FIELD          => $aliasName,
            IndexOperationEnum::EXCEPTION_FIELD      => $exception,
            IndexOperationEnum::OPERATION_TIME_FIELD => $operationTime,
        ];

        $this->level = LogLevel::ERROR;
    }
}
