<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\DocumentInsert;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvent;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\ImportBusinessEvent;
use Psr\Log\LogLevel;

/**
 * Log a business event that when inserting into the index, the merchants changed.
 */
class MerchantCountChanged implements BusinessEvent
{
    use ImportBusinessEvent;

    const COUNT_CHANGED_EVENT_MESSAGE = 'Merchants have changed before import finished';

    /**
     * MerchantCountChanged constructor.
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
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => self::COUNT_CHANGED_EVENT_MESSAGE,
        ];

        $this->level = LogLevel::WARNING;
    }
}
