<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Factory;

use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantFilterAggregate;
use MapleSyrupGroup\Search\Services\Client\SearchClient;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchSearch;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\IndexNameFactory;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory\BestMatchQueryFactory;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory\CategoryExactMatchQueryFactory;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory\CategoryPrefixMatchQueryFactory;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory\ExactMatchQueryFactory;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory\FallbackQueryFactory;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory\PrefixMatchQueryFactory;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory\RatesTextExactMatchQueryFactory;
use MapleSyrupGroup\Search\Services\Merchants\Search\BestMatchSearch;
use MapleSyrupGroup\Search\Services\Merchants\Search\CategoryExactMatchSearch;
use MapleSyrupGroup\Search\Services\Merchants\Search\CategoryPrefixMatchSearch;
use MapleSyrupGroup\Search\Services\Merchants\Search\ExactMatchSearch;
use MapleSyrupGroup\Search\Services\Merchants\Search\FallbackSearch;
use MapleSyrupGroup\Search\Services\Merchants\Search\PrefixMatchSearch;
use MapleSyrupGroup\Search\Services\Merchants\Search\Timer;

trait StrategyBuilder
{
    /**
     * @param Timer $timer
     *
     * @return ExactMatchSearch
     */
    public function getExactMatchSearchStrategy(Timer $timer)
    {
        return new ExactMatchSearch(
            new ElasticsearchSearch(
                new ExactMatchQueryFactory($this->getFilters()->getMerchantNameFilter()),
                $this->getIndexNameFactory(),
                $this->getSearchClient()
            ),
            $timer,
            self::STRATEGY_EXACT_MATCH
        );
    }

    /**
     * @param Timer $timer
     *
     * @return ExactMatchSearch
     */
    public function getRatesTextStrategy(Timer $timer)
    {
        return new ExactMatchSearch(
            new ElasticsearchSearch(
                new RatesTextExactMatchQueryFactory(),
                $this->getIndexNameFactory(),
                $this->getSearchClient()
            ),
            $timer,
            self::STRATEGY_RATES_EXACT_MATCH
        );
    }

    /**
     * @param Timer $timer
     *
     * @return CategoryExactMatchSearch
     */
    public function getCategoryPrefixMatchStrategy(Timer $timer)
    {
        return new CategoryPrefixMatchSearch(
            new ElasticsearchSearch(
                new CategoryPrefixMatchQueryFactory($this->getFilters()->getCategoryFilter()),
                $this->getIndexNameFactory(),
                $this->getSearchClient()
            ),
            $timer,
            self::STRATEGY_CATEGORY_PREFIX_MATCH
        );
    }

    /**
     * @param Timer $timer
     *
     * @return CategoryExactMatchSearch
     */
    public function getCategoryExactMatchStrategy(Timer $timer)
    {
        return new CategoryExactMatchSearch(
            new ElasticsearchSearch(
                new CategoryExactMatchQueryFactory($this->getFilters()->getCategoryFilter()),
                $this->getIndexNameFactory(),
                $this->getSearchClient()
            ),
            $timer,
            self::STRATEGY_CATEGORY_EXACT_MATCH
        );
    }

    /**
     * @param Timer $timer
     *
     * @return FallbackSearch
     */
    public function getFallbackSearchStrategy(Timer $timer)
    {
        return new FallbackSearch(
            new ElasticsearchSearch(
                new FallbackQueryFactory($this->getFilters()->getMerchantNameFilter()),
                $this->getIndexNameFactory(),
                $this->getSearchClient()
            ),
            $timer,
            self::STRATEGY_LAST_RESORT
        );
    }

    /**
     * @param Timer $timer
     *
     * @return BestMatchSearch
     */
    public function getBestMatchSearchStrategy(Timer $timer)
    {
        return new BestMatchSearch(
            new ElasticsearchSearch(
                new BestMatchQueryFactory($this->getFilters()->getMerchantNameFilter()),
                $this->getIndexNameFactory(),
                $this->getSearchClient()
            ),
            $timer,
            self::STRATEGY_MOST_RELEVANT
        );
    }

    /**
     * @param Timer $timer
     *
     * @return PrefixMatchSearch
     */
    protected function getPrefixMatchSearchStrategy(Timer $timer)
    {
        return new PrefixMatchSearch(
            new ElasticsearchSearch(
                new PrefixMatchQueryFactory($this->getFilters()->getMerchantNameFilter()),
                $this->getIndexNameFactory(),
                $this->getSearchClient()
            ),
            $timer,
            self::STRATEGY_PREFIX_MATCH
        );
    }

    /**
     * @return IndexNameFactory
     */
    abstract public function getIndexNameFactory();

    /**
     * @return SearchClient
     */
    abstract public function getSearchClient();

    /**
     * @return MerchantFilterAggregate
     */
    abstract public function getFilters();
}