<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\DocumentInsert;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvent;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\ImportBusinessEvent;

/**
 * Business Event to indicate an import succeeded.
 */
class Success implements BusinessEvent
{
    use ImportBusinessEvent;

    const IMPORT_SUCCESS_MESSAGE = 'Successfully imported documents';

    /**
     * Success constructor.
     *
     * @param string $indexName
     * @param string $typeName
     * @param string $documentTotal
     * @param float  $operationDuration
     */
    public function __construct($indexName, $typeName, $documentTotal, $operationDuration)
    {
        $this->context = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::INDEX_FIELD          => $indexName,
            IndexOperationEnum::TYPE_FIELD           => $typeName,
            IndexOperationEnum::DOCUMENT_COUNT_FIELD => $documentTotal,
            IndexOperationEnum::OPERATION_TIME_FIELD => $operationDuration,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => self::IMPORT_SUCCESS_MESSAGE,
        ];
    }
}
