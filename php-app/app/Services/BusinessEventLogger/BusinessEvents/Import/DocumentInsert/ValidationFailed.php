<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\DocumentInsert;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvent;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\ImportBusinessEvent;
use Psr\Log\LogLevel;

/**
 * Log an event when we check that an insert occurred and find it did not.
 */
class ValidationFailed implements BusinessEvent
{
    use ImportBusinessEvent;

    const VALIDATION_FAILURE_EVENT_MESSAGE = 'Index contains an unexpected amount of documents';

    /**
     * ValidationFailed constructor.
     *
     * @param string $indexName
     * @param string $typeName
     * @param string $expected
     * @param string $actual
     */
    public function __construct($indexName, $typeName, $expected, $actual)
    {
        $this->context = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::INDEX_FIELD          => $indexName,
            IndexOperationEnum::EXPECTED_VALUE_FIELD => $expected,
            IndexOperationEnum::ACTUAL_VALUE_FIELD   => $actual,
            IndexOperationEnum::TYPE_FIELD           => $typeName,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => self::VALIDATION_FAILURE_EVENT_MESSAGE,
        ];

        $this->level = LogLevel::WARNING;
    }
}
