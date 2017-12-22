<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Search;

use MapleSyrupGroup\Search\Services\Merchants\Factory\SearchFactory;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\Search;

class PrefixMatchTest extends \PHPUnit_Framework_TestCase
{

    public function testThatSearchResultSetIsReturnedWhenThereIsAPrefixMatch()
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

        $strategy = new PrefixMatchSearch(
            $search->reveal(),
            new Timer(),
            SearchFactory::STRATEGY_PREFIX_MATCH
        );
        $response = $strategy->search($query);

        $this->assertArrayHasKey('hits', $response);
        $this->assertSame($results['hits'], $response['hits']);
    }
}
