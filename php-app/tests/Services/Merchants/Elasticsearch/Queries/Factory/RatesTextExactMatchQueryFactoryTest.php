<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\RatesTextExactMatchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;

class RatesTextExactMatchQueryFactoryTest extends QueryFactoryTestCase
{
    protected function createQueryFactory()
    {
        return new RatesTextExactMatchQueryFactory();
    }

    public function testItCreatesTheRatesTextExactMatchQuery()
    {
        $query = new Query(self::TERM, self::DOMAIN_ID, new SortParameter());
        $query->setLanguage(self::ENGLISH);
        $esQuery = $this->queryFactory->create($query);

        $this->assertInstanceOf(RatesTextExactMatchQuery::class, $esQuery);
    }
}
