<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Search;

use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\Search;

/**
 * Class AbstractSearch.
 */
abstract class AbstractSearch implements Search
{
    /**
     * @var Search
     */
    private $search;

    /**
     * @var Timer
     */
    private $timer;

    /**
     * @var string
     */
    private $name;

    /**
     * AbstractSearch constructor.
     *
     * @param Search $search
     * @param Timer  $timer
     * @param string $name
     */
    public function __construct(Search $search, Timer $timer, $name)
    {
        $this->setName($name);
        $this->setTimer($timer);
        $this->setSearch($search);
    }

    /**
     * @param Query $query
     *
     * @return array
     */
    public function search(Query $query)
    {
        $this->getTimer()->searchStarted($this->getName());

        $resultSet = $this->getSearch()->search($query);

        $this->getTimer()->searchFinished($this->getName());

        if ($this->isValid($resultSet)) {
            return $this->postProcessResultSet($resultSet);
        }

        throw new SearchCriteriaNotMetException(sprintf($this->getErrorMessage(), $resultSet['hits']['total']));
    }

    /**
     * @param array $resultSet
     *
     * @return array
     */
    protected function postProcessResultSet(array $resultSet)
    {
        $resultSet['strategy']             = $this->getName();
        $resultSet['attempted_strategies'] = $this->getTimer()->getSearchAttempts();

        return $resultSet;
    }

    /**
     * @param array $resultSet
     *
     * @return bool
     */
    protected function isValid(array $resultSet)
    {
        return $resultSet['hits']['total'] > 0;
    }

    /**
     * @return Search
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param Search $search
     */
    protected function setSearch($search)
    {
        $this->search = $search;
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
     */
    protected function setTimer($timer)
    {
        $this->timer = $timer;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    protected function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    abstract protected function getErrorMessage();
}
