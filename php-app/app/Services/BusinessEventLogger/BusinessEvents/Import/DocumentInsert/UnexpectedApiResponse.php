<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\DocumentInsert;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvent;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\ImportBusinessEvent;
use Psr\Log\LogLevel;

/**
 * Log a business event when the API returns a weird response.
 */
class UnexpectedApiResponse implements BusinessEvent
{
    use ImportBusinessEvent;

    const UNEXPECTED_RESPONSE_EVENT_MESSAGE = 'Unexpected response from API';

    /**
     * UnexpectedApiResponse constructor.
     *
     * @param mixed  $result
     * @param string $indexName
     * @param string $typeName
     */
    public function __construct($result, $indexName, $typeName)
    {
        $this->context = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::API_RESPONSE_FIELD   => $result,
            IndexOperationEnum::INDEX_FIELD          => $indexName,
            IndexOperationEnum::TYPE_FIELD           => $typeName,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => self::UNEXPECTED_RESPONSE_EVENT_MESSAGE,
        ];

        $this->level = LogLevel::WARNING;
    }
}
