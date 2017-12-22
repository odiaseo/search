<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\BestMatchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\FrenchBestMatchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;

class BestMatchQueryFactoryTest extends QueryFactoryTestCase
{
    protected function createQueryFactory()
    {
        return new BestMatchQueryFactory();
    }

    public function testItCreatesTheBestMatchQueryEnglishForTheEnglishLanguage()
    {
        $query = new Query(self::TERM, self::DOMAIN_ID, new SortParameter());
        $query->setLanguage(self::ENGLISH);
        $esQuery = $this->queryFactory->create($query);

        $this->assertInstanceOf(BestMatchQuery::class, $esQuery);
    }

    public function testItCreatesTheBestMatchQueryFrenchForTheFrenchLanguage()
    {
        $query = new Query(self::TERM, self::DOMAIN_ID, new SortParameter());
        $query->setLanguage(self::FRENCH);
        $esQuery = $this->queryFactory->create($query);

        $this->assertInstanceOf(BestMatchQuery::class, $esQuery);
    }
}
