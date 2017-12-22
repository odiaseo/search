<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvent;

/**
 * Import started business event.
 */
class Started implements BusinessEvent
{
    use ImportBusinessEvent;

    const INDEX_STARTED_EVENT_MESSAGE = 'Merchant Index Builder Initiated';

    /**
     * Started constructor.
     *
     * @param string $indexName
     * @param string $aliasName
     */
    public function __construct($indexName, $aliasName)
    {
        $this->context = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::INDEX_FIELD          => $indexName,
            IndexOperationEnum::ALIAS_FIELD          => $aliasName,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => self::INDEX_STARTED_EVENT_MESSAGE,
        ];
    }
}
