<?php

namespace MapleSyrupGroup\Search\Enums\Logging;

/**
 * The standard fields for logging into ElasticSearch for searches
 *
 * @package MapleSyrupGroup\Search\Enums\Logging
 */
final class MerchantSearchEnum
{
    /**
     * Unique identifier to allow grouping of multiple search log events to a single search 'instance'.
     */
    const SEARCH_INSTANCE_ID_FIELD = 'search_instance_id';

    /**
     * Number of merchants available in result set where a 'merchant' search is performed.
     */
    const NUM_MERCHANTS_FIELD = 'search_num_merchants';

    /**
     * Either 'QuidCo' or 'Become' to indicate whether a 'local' Quidco Merchant / Product Index search or a 'remote'
     * Become API search.
     */
    const SEARCH_PLATFORM_FIELD = 'search_platform';
    /**
     * Indicates a 'local' Quidco Merchant or product search
     */
    const SEARCH_PLATFORM_VALUE = 'QuidCo';

    /**
     * A list of the Quidco Merchant IDs of the top 20 search results
     */
    const SEARCH_RESULTS_FIELD = 'search_results';

    /**
     * A list of the Quidco Merchant IDs of the top 20 search results
     */
    const SEARCH_RESULTS_NAMES_FIELD = 'search_results_names';
    /**
     * In the case of a 'merchant' search this can have the values of 'exact_match', 'most_relevant' or 'last_resort'/
     */
    const SEARCH_STRATEGY_FIELD = 'search_subtype';

    /**
     * The 'term' used for the search. This will usually be the text string used to search but in the case of a
     * 'merchant' based product search this will show the Merchant ID used for the search.
     */
    const SEARCH_TERM_FIELD = 'search_term';

    /**
     * Number of milliseconds that the search operation took. In some cases this will be the amount of time taken by the
     * actual ES search but in other cases will be the amount of overall time that a particular PHP script execution has
     * taken.
     */
    const SEARCH_TIME_FIELD = 'search_time_ms';

    /**
     * The Quidco merchant ID of the top search result.
     */
    const SEARCH_TOP_RESULT_FIELD = 'search_top_result';

    /**
     * The name of the top merchant result.
     */
    const SEARCH_TOP_RESULT_NAME_FIELD = 'search_top_result_name';

    /**
     * Either 'product' or 'merchant' depending on which index is being searched. For Quidco based indices this could be
     * 'merchant' or 'product'. For Become API searches this is always 'product'.
     */
    const SEARCH_TYPE_FIELD = 'search_type';

    /**
     * This is only the merchant search so this is always merchant
     */
    const SEARCH_TYPE_VALUE = 'merchant';

    /**
     * Overall search version employed by the search. This is used to be able to monitor the incremental rollout of V4
     * search by checking proportion of V3 to V4 searches occurring as V4 was opened up to more visitors.
     */
    const VERSION_FIELD = 'search_version';

    /**
     * Overall search version employed by the search. This is used to be able to monitor the incremental rollout
     * versions
     */
    const VERSION_VALUE = 'v4-q-platform-search';

    /**
     * user_id If no user logged in or unable to establish user ID from session this will have a value of 0. Otherwise
     * it will be the user_id of the user performing the search.
     */
    const USER_ID_FIELD = 'user_id';

    /**
     * We never know which user we have at this level so it's always 0
     */
    const USER_ID_VALUE = 0;
}
