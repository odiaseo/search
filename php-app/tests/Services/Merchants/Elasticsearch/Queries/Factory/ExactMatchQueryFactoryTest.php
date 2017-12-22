<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\MerchantExactMatchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;

class ExactMatchQueryFactoryTest extends QueryFactoryTestCase
{
    protected function createQueryFactory()
    {
        return new ExactMatchQueryFactory();
    }

    public function testItCreatesTheExactMatchQueryEnglishForTheEnglishLanguage()
    {
        $query = new Query(self::TERM, self::DOMAIN_ID, new SortParameter());
        $query->setLanguage(self::ENGLISH);
        $esQuery = $this->queryFactory->create($query);

        $this->assertInstanceOf(MerchantExactMatchQuery::class, $esQuery);
    }

    public function testItCreatesTheExactMatchQueryFrenchForTheFrenchLanguage()
    {
        $query = new Query(self::TERM, self::DOMAIN_ID, new SortParameter());
        $query->setLanguage(self::FRENCH);

        $esQuery = $this->queryFactory->create($query);

        $this->assertInstanceOf(MerchantExactMatchQuery::class, $esQuery);
    }
}
