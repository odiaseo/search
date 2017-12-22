<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\MerchantPrefixMatchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;

class PrefixMatchQueryFactoryTest extends QueryFactoryTestCase
{
    protected function createQueryFactory()
    {
        return new PrefixMatchQueryFactory();
    }

    public function testItCreatesTheExactMatchQueryEnglishForTheEnglishLanguage()
    {
        $query = new Query(self::TERM, self::DOMAIN_ID, new SortParameter());
        $query->setLanguage(self::ENGLISH);
        $esQuery = $this->queryFactory->create($query);

        $this->assertInstanceOf(MerchantPrefixMatchQuery::class, $esQuery);
    }

    public function testItCreatesTheExactMatchQueryFrenchForTheFrenchLanguage()
    {
        $query = new Query(self::TERM, self::DOMAIN_ID, new SortParameter());
        $query->setLanguage(self::FRENCH);

        $esQuery = $this->queryFactory->create($query);

        $this->assertInstanceOf(MerchantPrefixMatchQuery::class, $esQuery);
    }
}
