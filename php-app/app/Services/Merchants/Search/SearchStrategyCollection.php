<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Search;

use MapleSyrupGroup\Search\Exceptions\SearchException;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Search\SearchCompletedEvent;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\Search;

class SearchStrategyCollection implements Search
{
    /**
     * @var Search[]
     */
    private $strategies;

    /**
     * @var BusinessEventLogger
     */
    private $eventLogger;

    /**
     * @var Timer
     */
    private $timer;

    /**
     * @var array
     */
    private $domainStrategies = [];

    /**
     * @var bool
     */
    private $enableLogging;

    /**
     * @param Search[]            $strategies
     * @param BusinessEventLogger $logger
     * @param Timer               $timer
     * @param array               $domainStrategies
     * @param bool                $enableLogging
     */
    public function __construct(
        array $strategies,
        BusinessEventLogger $logger,
        Timer $timer,
        array $domainStrategies,
        $enableLogging
    ) {
        $this->setStrategies($strategies);
        $this->setEventLogger($logger);
        $this->setTimer($timer);
        $this->setDomainStrategies($domainStrategies);
        $this->setEnableLogging($enableLogging);
    }

    /**
     * @param Query $query
     *
     * @return array
     */
    public function search(Query $query)
    {
        $list       = [];
        $exception  = null;
        $strategies = $this->filterStrategies(
            $this->getEnabledStrategies($query->getDomainId()),
            $query->getStrategy()
        );
        foreach ($strategies as $name => $strategy) {
            try {
                $list[] = $name;

                return $this->trySearch($name, $strategy, $query);
            } catch (SearchException $exception) {
                $this->getTimer()->searchFinished($name);
            }
        }

        $message = sprintf('The following attempted strategies did not return any result: [%s].', implode(', ', $list));
        throw new SearchCriteriaNotMetException($message, 0, $exception);
    }

    /**
     * Get list of enabled strategies for the specified domain.
     *
     * @param int $domainId
     *
     * @return array
     */
    private function getEnabledStrategies($domainId)
    {
        $domainStrategies = $this->getDomainStrategies();
        if (empty($domainStrategies[$domainId]) || !is_array($domainStrategies[$domainId])) {
            throw new \InvalidArgumentException('Invalid strategy was configured for this domain');
        }

        $enabled = array_filter(
            $this->getStrategies(),
            function ($key) use ($domainId, $domainStrategies) {
                return in_array($key, $domainStrategies[$domainId]);
            },
            ARRAY_FILTER_USE_KEY
        );

        if (empty($enabled)) {
            throw new \InvalidArgumentException('No valid search strategy was found for this domain');
        }

        return $enabled;
    }

    /**
     * @param array $enabled
     * @param mixed $requested
     *
     * @return array
     */
    private function filterStrategies(array $enabled, $requested)
    {
        if ($requested === null) {
            return $enabled;
        }

        $filtered = array_filter(
            $enabled,
            function ($key) use ($requested) {
                return in_array($key, $requested);
            },
            ARRAY_FILTER_USE_KEY
        );

        return $filtered;
    }

    /**
     * @param string $name
     * @param Search $strategy
     * @param Query  $query
     *
     * @return array
     */
    private function trySearch($name, Search $strategy, Query $query)
    {
        $timer                             = $this->getTimer();
        $resultSet                         = $strategy->search($query);
        $resultSet['strategy']             = $name;
        $resultSet['attempted_strategies'] = $timer->getSearchAttempts();

        if ($this->isEnableLogging()) {
            $this->getEventLogger()->log(
                new SearchCompletedEvent($query->getSearchTerm(), $resultSet, $name, $timer->getTimeTaken($name))
            );
        }

        return $resultSet;
    }

    /**
     * @return \MapleSyrupGroup\Search\Services\Merchants\Search[]
     */
    public function getStrategies()
    {
        return $this->strategies;
    }

    /**
     * @param \MapleSyrupGroup\Search\Services\Merchants\Search[] $strategies
     *
     * @return SearchStrategyCollection
     */
    private function setStrategies(array $strategies)
    {
        $this->strategies = $strategies;

        return $this;
    }

    /**
     * @return BusinessEventLogger
     */
    public function getEventLogger()
    {
        return $this->eventLogger;
    }

    /**
     * @param BusinessEventLogger $eventLogger
     *
     * @return SearchStrategyCollection
     */
    private function setEventLogger(BusinessEventLogger $eventLogger)
    {
        $this->eventLogger = $eventLogger;

        return $this;
    }

    /**
     * @return Timer
     */
    public function getTimer()
    {
        return $this->timer;
    }

    /**
     * @param Timer $timer
     *
     * @return SearchStrategyCollection
     */
    private function setTimer(Timer $timer)
    {
        $this->timer = $timer;

        return $this;
    }

    /**
     * @return array
     */
    public function getDomainStrategies()
    {
        return $this->domainStrategies;
    }

    /**
     * @param array $domainStrategies
     *
     * @return SearchStrategyCollection
     */
    private function setDomainStrategies(array $domainStrategies)
    {
        $this->domainStrategies = $domainStrategies;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnableLogging()
    {
        return $this->enableLogging;
    }

    /**
     * @param bool $enableLogging
     */
    private function setEnableLogging($enableLogging)
    {
        $this->enableLogging = $enableLogging;
    }
}
