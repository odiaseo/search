<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Search;

use MapleSyrupGroup\Search\Services\Merchants\Factory\SearchFactory;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\Search;

class CategoryExactMatchTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \MapleSyrupGroup\Search\Services\Merchants\Search\SearchCriteriaNotMetException
     */
    public function testThatExceptionIsThrownWhenNoResultIsFound()
    {
        $results = [
            'hits' => [
                'total' => 0,
            ],
        ];
        $query   = $this->prophesize(Query::class)->reveal();
        $search  = $this->prophesize(Search::class);
        $search->search($query)->willReturn($results);

        $strategy = new CategoryExactMatchSearch(
            $search->reveal(), new Timer(), SearchFactory::STRATEGY_CATEGORY_EXACT_MATCH
        );
        $strategy->search($query);
    }

    public function testThatSearchResultSetIsReturnedWhenThereIsAMatch()
    {
        $results = [
            'hits' => [
                'total' => 1,
                'hits'  => [
                    [
                        'id' => 1,
                    ],
                ],
            ],
        ];
        $query   = $this->prophesize(Query::class)->reveal();
        $search  = $this->prophesize(Search::class);
        $search->search($query)->willReturn($results);

        $strategy = new CategoryExactMatchSearch(
            $search->reveal(), new Timer(), SearchFactory::STRATEGY_CATEGORY_EXACT_MATCH
        );
        $response = $strategy->search($query);

        $this->assertArrayHasKey('hits', $response);
        $this->assertSame($results['hits'], $response['hits']);
    }
}
