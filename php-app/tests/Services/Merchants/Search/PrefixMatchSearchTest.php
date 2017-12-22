<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Search;

use MapleSyrupGroup\Search\Services\Merchants\Factory\SearchFactory;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\Search;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;
use Prophecy\Prophecy\ObjectProphecy;
use PHPUnit\Framework\TestCase;

class PrefixMatchSearchTest extends TestCase
{
    /**
     * @var PrefixMatchSearch
     */
    private $search;

    /**
     * @var Search|ObjectProphecy
     */
    private $decoratedSearch;

    protected function setUp()
    {
        $this->decoratedSearch = $this->prophesize(Search::class);
        $this->search          = new PrefixMatchSearch(
            $this->decoratedSearch->reveal(), new Timer(), SearchFactory::STRATEGY_PREFIX_MATCH
        );
    }

    public function testItIsSearch()
    {
        $this->assertInstanceOf(Search::class, $this->search);
    }

    public function testItCallsTheDecoratedSearchToFetchTheResultSet()
    {
        $query             = $this->createQuery();
        $expectedResultSet = $this->createResultSet($hits = 1);

        $this->decoratedSearch->search($query)->willReturn($expectedResultSet);

        $resultSet = $this->search->search($query);

        $this->assertArrayHasKey('hits', $resultSet);
        $this->assertSame($expectedResultSet['hits'], $resultSet['hits']);
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
