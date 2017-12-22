<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Factory;

use MapleSyrupGroup\QCommon\Enum\DomainEnum;
use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantFilterAggregate;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger as Logger;
use MapleSyrupGroup\Search\Services\Client\SearchClient;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\IndexNameFactory;
use MapleSyrupGroup\Search\Services\Merchants\Search;
use MapleSyrupGroup\Search\Services\Merchants\Search\SearchStrategyCollection;
use MapleSyrupGroup\Search\Services\Merchants\Search\Timer;

/**
 * Setup search factory with configured strategies
 * Not all strategies applies to all domains.
 */
class SearchFactory
{
    use StrategyBuilder;

    const STRATEGY_EXACT_MATCH           = 'exact_match';
    const STRATEGY_PREFIX_MATCH          = 'prefix_match';
    const STRATEGY_RATES_EXACT_MATCH     = 'rates_text_exact_match';
    const STRATEGY_MOST_RELEVANT         = 'most_relevant';
    const STRATEGY_LAST_RESORT           = 'last_resort';
    const STRATEGY_CATEGORY_EXACT_MATCH  = 'category_exact_match';
    const STRATEGY_CATEGORY_PREFIX_MATCH = 'category_prefix_match';

    /**
     * @var MerchantFilterAggregate
     */
    public $filters;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var SearchClient
     */
    protected $searchClient;

    /**
     * @var IndexNameFactory
     */
    protected $indexNameFactory;

    /**
     * @var bool
     */
    protected $logStrategyEvents = false;

    /**
     * Search strategies enabled for each search domain.
     *
     * @var array
     */
    protected $domainStrategies = [
        DomainEnum::DOMAIN_ID_SHOOP  => [
            self::STRATEGY_PREFIX_MATCH,
            self::STRATEGY_EXACT_MATCH,
            self::STRATEGY_CATEGORY_EXACT_MATCH,
            self::STRATEGY_CATEGORY_PREFIX_MATCH,
            self::STRATEGY_MOST_RELEVANT,
            self::STRATEGY_LAST_RESORT,
        ],
        DomainEnum::DOMAIN_ID_QUIDCO => [
            self::STRATEGY_PREFIX_MATCH,
            self::STRATEGY_EXACT_MATCH,
            self::STRATEGY_CATEGORY_EXACT_MATCH,
            self::STRATEGY_CATEGORY_PREFIX_MATCH,
            self::STRATEGY_MOST_RELEVANT,
            self::STRATEGY_LAST_RESORT,
        ],
    ];

    /**
     * SearchFactory constructor.
     *
     * @param Logger                  $logger
     * @param SearchClient            $client
     * @param IndexNameFactory        $nameFactory
     * @param MerchantFilterAggregate $filters
     * @param $logEvents
     */
    public function __construct(
        Logger $logger,
        SearchClient $client,
        IndexNameFactory $nameFactory,
        MerchantFilterAggregate $filters,
        $logEvents
    ) {
        $this->setLogger($logger);
        $this->setIndexNameFactory($nameFactory);
        $this->setSearchClient($client);
        $this->setLogStrategyEvents($logEvents);
        $this->setFilters($filters);
    }

    /**
     * @return Search
     */
    public function createSearch()
    {
        $timer = new Timer();
        $list  = $this->getDefinedStrategies($timer);

        return new SearchStrategyCollection(
            $list,
            $this->getLogger(),
            $timer,
            $this->getDomainStrategies(),
            $this->isLogStrategyEvents()
        );
    }

    /**
     * Returns the defined search strategies in order of priority.
     *
     * @param Timer $timer
     *
     * @return array
     */
    protected function getDefinedStrategies(Timer $timer)
    {
        return [
            self::STRATEGY_CATEGORY_PREFIX_MATCH => $this->getCategoryPrefixMatchStrategy($timer),
            self::STRATEGY_CATEGORY_EXACT_MATCH  => $this->getCategoryExactMatchStrategy($timer),
            self::STRATEGY_PREFIX_MATCH          => $this->getPrefixMatchSearchStrategy($timer),
            self::STRATEGY_EXACT_MATCH           => $this->getExactMatchSearchStrategy($timer),
            self::STRATEGY_RATES_EXACT_MATCH     => $this->getRatesTextStrategy($timer),
            self::STRATEGY_MOST_RELEVANT         => $this->getBestMatchSearchStrategy($timer),
            self::STRATEGY_LAST_RESORT           => $this->getFallbackSearchStrategy($timer),
        ];
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return SearchClient
     */
    public function getSearchClient()
    {
        return $this->searchClient;
    }

    /**
     * @param SearchClient $searchClient
     */
    public function setSearchClient($searchClient)
    {
        $this->searchClient = $searchClient;
    }

    /**
     * @return IndexNameFactory
     */
    public function getIndexNameFactory()
    {
        return $this->indexNameFactory;
    }

    /**
     * @param IndexNameFactory $indexNameFactory
     */
    public function setIndexNameFactory($indexNameFactory)
    {
        $this->indexNameFactory = $indexNameFactory;
    }

    /**
     * @return array
     */
    public function getDomainStrategies()
    {
        return $this->domainStrategies;
    }

    /**
     * @return bool
     */
    public function isLogStrategyEvents()
    {
        return $this->logStrategyEvents;
    }

    /**
     * @param bool $logStrategyEvents
     */
    private function setLogStrategyEvents($logStrategyEvents)
    {
        $this->logStrategyEvents = $logStrategyEvents;
    }

    /**
     * @return MerchantFilterAggregate
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param MerchantFilterAggregate $filters
     *
     * @return $this
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;

        return $this;
    }
}
