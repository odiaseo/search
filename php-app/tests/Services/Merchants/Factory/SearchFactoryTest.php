<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Factory;

use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantFilterAggregate;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger;
use MapleSyrupGroup\Search\Services\Client\SearchClient;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\IndexNameFactory;
use MapleSyrupGroup\Search\Services\Merchants\Search;

class SearchFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testItCreatesTheSearchService()
    {
        $logger = $this->prophesize(BusinessEventLogger::class)->reveal();
        $elastica = $this->prophesize(SearchClient::class)->reveal();
        $filters = $this->prophesize(MerchantFilterAggregate::class);
        $indexNameFactory = $this->prophesize(IndexNameFactory::class)->reveal();

        $factory = new SearchFactory($logger, $elastica, $indexNameFactory, $filters->reveal(), false);

        $search = $factory->createSearch();

        $this->assertInstanceOf(Search::class, $search);
    }
}
