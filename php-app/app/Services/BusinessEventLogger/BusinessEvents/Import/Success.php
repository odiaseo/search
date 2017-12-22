<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvent;

/**
 * Import succeeded business event.
 */
class Success implements BusinessEvent
{
    use ImportBusinessEvent;

    const SUCCESS_EVENT_MESSAGE = 'Search Index Build Success';

    /**
     * Success constructor.
     *
     * @param string $indexName
     * @param string $aliasName
     * @param array  $oldIndexes
     * @param float  $operationTime
     * @param int $documentCount
     */
    public function __construct($indexName, $aliasName, array $oldIndexes, $operationTime, $documentCount)
    {
        $this->context = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::INDEX_FIELD          => $indexName,
            IndexOperationEnum::ALIAS_FIELD          => $aliasName,
            IndexOperationEnum::REPLACED_INDEX_FIELD => $oldIndexes,
            IndexOperationEnum::OPERATION_TIME_FIELD => $operationTime,
            IndexOperationEnum::DOCUMENT_COUNT_FIELD => $documentCount,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => self::SUCCESS_EVENT_MESSAGE,
        ];
    }
}
