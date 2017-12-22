<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Search;

use MapleSyrupGroup\Search\Enums\Logging\MerchantSearchEnum;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvent;
use Psr\Log\LogLevel;

/**
 * Search completed business event
 *
 * @package MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Search
 */
class SearchCompletedEvent implements BusinessEvent
{
    const EVENT_MESSAGE = 'search_stats_legacy';
    const ID_FIELD = 'id';
    const NAME_FIELD = 'name';

    const SOURCE_KEY = '_source';
    const HITS_KEY = 'hits';
    const TOTAL_KEY = 'total';

    const LIMIT = 20;
    /**
     * @var array
     */
    private $context;


    /**
     * SearchCompletedEvent constructor.
     *
     * @param string $term
     * @param array $response
     * @param string $searchStrategy
     * @param float $elapsedSeconds
     */
    public function __construct($term, array $response, $searchStrategy, $elapsedSeconds)
    {
        $top20Ids = $this->getFieldListFromResponse($response, self::ID_FIELD);
        $top20Names = $this->getFieldListFromResponse($response, self::NAME_FIELD);

        $this->context = [
            MerchantSearchEnum::SEARCH_INSTANCE_ID_FIELD     => uniqid(),
            MerchantSearchEnum::NUM_MERCHANTS_FIELD          => $this->getTotalHits($response),
            MerchantSearchEnum::SEARCH_PLATFORM_FIELD        => MerchantSearchEnum::SEARCH_PLATFORM_VALUE,
            MerchantSearchEnum::SEARCH_RESULTS_FIELD         => $top20Ids,
            MerchantSearchEnum::SEARCH_RESULTS_NAMES_FIELD   => $top20Names,
            MerchantSearchEnum::SEARCH_STRATEGY_FIELD        => $searchStrategy,
            MerchantSearchEnum::SEARCH_TERM_FIELD            => $term,
            MerchantSearchEnum::SEARCH_TIME_FIELD            => $elapsedSeconds,
            MerchantSearchEnum::SEARCH_TOP_RESULT_FIELD      => isset($top20Ids[0]) ? $top20Ids[0] : null,
            MerchantSearchEnum::SEARCH_TOP_RESULT_NAME_FIELD => isset($top20Names[0]) ? $top20Names[0] : null,
            MerchantSearchEnum::SEARCH_TYPE_FIELD            => MerchantSearchEnum::SEARCH_TYPE_VALUE,
            MerchantSearchEnum::VERSION_FIELD                => MerchantSearchEnum::VERSION_VALUE,
            MerchantSearchEnum::USER_ID_FIELD                => MerchantSearchEnum::USER_ID_VALUE,
        ];
    }

    /**
     * Get the total number of hits
     *
     * @param array $response
     * @return int
     */
    private function getTotalHits(array $response)
    {
        if (isset($response[self::HITS_KEY][self::TOTAL_KEY])) {
            return (int)$response[self::HITS_KEY][self::TOTAL_KEY];
        }

        return 0;
    }

    /**
     * Return specified field for each valid item in the result set
     *
     * @param array $response
     * @param $field
     * @param int $limit
     * @return array
     */
    private function getFieldListFromResponse(array $response, $field, $limit = self::LIMIT)
    {
        $list = [];
        $count = 0;

        if (!isset($response[self::HITS_KEY][self::HITS_KEY]) || !is_array($response[self::HITS_KEY][self::HITS_KEY])) {
            return $list;
        }

        foreach ($response[self::HITS_KEY][self::HITS_KEY] as $item) {
            if (isset($item[self::SOURCE_KEY][$field])) {
                $list[] = $item[self::SOURCE_KEY][$field];
                $count++;
            }

            if ($count >= $limit) {
                break;
            }
        }

        return $list;
    }

    /**
     * A log level as defined by LogLevel
     *
     * @see \Psr\Log\LogLevel
     *
     * @return string
     */
    public function getLevel()
    {
        return LogLevel::DEBUG;
    }

    /**
     * A human readable string for this particular event.
     *
     * @return string
     */
    public function getMessage()
    {
        return self::EVENT_MESSAGE;
    }

    /**
     * An array of JSON serializable object or exceptions or context that provide information about this business event
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }
}
