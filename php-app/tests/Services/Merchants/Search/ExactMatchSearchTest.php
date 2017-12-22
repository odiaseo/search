<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Search;

use MapleSyrupGroup\Search\Services\Merchants\Factory\SearchFactory;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\Search;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;
use Prophecy\Prophecy\ObjectProphecy;

class ExactMatchSearchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExactMatchSearch
     */
    private $search;

    /**
     * @var Search|ObjectProphecy
     */
    private $decoratedSearch;

    protected function setUp()
    {
        $this->decoratedSearch = $this->prophesize(Search::class);
        $this->search = new ExactMatchSearch($this->decoratedSearch->reveal(),new Timer(), SearchFactory::STRATEGY_EXACT_MATCH);
    }

    public function testItIsSearch()
    {
        $this->assertInstanceOf(Search::class, $this->search);
    }

    public function testItCallsTheDecoratedSearchToFetchTheResultSet()
    {
        $query = $this->createQuery();
        $expectedResultSet = $this->createResultSet($hits = 1);

        $this->decoratedSearch->search($query)->willReturn($expectedResultSet);

        $resultSet = $this->search->search($query);

        $this->assertArrayHasKey('hits', $resultSet);
        $this->assertSame($expectedResultSet['hits'], $resultSet['hits']);
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Services\Merchants\Search\SearchCriteriaNotMetException
     */
    public function testItThrowsTheSearchCriteriaNotMetExceptionIfThereIsLessThanOneHit()
    {
        $query = $this->createQuery();
        $expectedResultSet = $this->createResultSet($hits = 0);

        $this->decoratedSearch->search($query)->willReturn($expectedResultSet);

        $this->search->search($query);
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Services\Merchants\Search\SearchCriteriaNotMetException
     */
    public function testItThrowsTheSearchCriteriaNotMetExceptionIfThereIsMoreThanOneHit()
    {
        $query = $this->createQuery();
        $expectedResultSet = $this->createResultSet($hits = 2);

        $this->decoratedSearch->search($query)->willReturn($expectedResultSet);

        $this->search->search($query);
    }

    private function createResultSet($totalHits)
    {
        return ['hits' => ['total' => $totalHits]];
    }

    private function createQuery()
    {
        return new Query('foo', 1, new SortParameter());
    }
}
