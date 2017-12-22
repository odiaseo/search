<?php

namespace MapleSyrupGroup\Search\Enums\Logging;

/**
 * Contains all the fields that we log using for Insert operations into the index
 *
 * @package MapleSyrupGroup\Search\Enums\Logging
 */
final class IndexOperationEnum
{
    /**
     * How long the operation took in seconds
     */
    const OPERATION_TIME_FIELD = 'operation_time_s';

    /**
     * Operation type field
     */
    const OPERATION_TYPE_FIELD = 'operation_type';

    /**
     * Operation occurring the we're logging about
     */
    const OPERATION_TYPE_VALUE_INDEX = 'INDEX';

    /**
     * All exceptions come in on this field
     */
    const EXCEPTION_FIELD = 'exception';

    /**
     * Human text to describe this event
     */
    const EVENT_MESSAGE_FIELD = 'event_message';

    /**
     * Any API responses will appear in this field
     */
    const API_RESPONSE_FIELD = 'api_response';

    /**
     * The current index we're operating on
     */
    const INDEX_FIELD = 'search_index_name';

    /**
     * The Alias this operation will change in the future, or has changed in the past
     */
    const ALIAS_FIELD = 'for_search_alias';

    /**
     * When we get a validation error, what we were expecting
     */
    const EXPECTED_VALUE_FIELD = 'expected_value';

    /**
     * When we get a validation error, what we got
     */
    const ACTUAL_VALUE_FIELD = 'actual_value';

    /**
     * The number of retries left we can retry the current operation
     */
    const RETRIES_LEFT_FIELD = 'retries_left';

    /**
     * The number of retries we had at the start
     */
    const TOTAL_RETRIES = 'total_retries';

    /**
     * Number of seconds until we retry again
     */
    const SECONDS_TO_RETRY = 'in_x_seconds';

    /**
     * Which index this index replaces value is an array
     */
    const REPLACED_INDEX_FIELD = 'replaced_search_index';

    /**
     * The type we're currently operating on
     */
    const TYPE_FIELD = "index_type";

    /**
     * The domain ID we're currently inserting documents for
     */
    const DOMAIN_ID_FIELD = 'domain_id';

    /**
     * Number of ES documents involved in the activity. This would refer to total of all merchants indexed.
     */
    const DOCUMENT_COUNT_FIELD = 'num_docs';
}
