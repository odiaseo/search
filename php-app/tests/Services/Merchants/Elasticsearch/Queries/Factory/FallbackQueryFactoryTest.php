<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\FallbackQuery;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\FrenchFallbackQuery;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;

class FallbackQueryFactoryTest extends QueryFactoryTestCase
{
    protected function createQueryFactory()
    {
        return new FallbackQueryFactory();
    }

    public function testItCreatesTheFallbackQueryEnglishForTheEnglishLanguage()
    {
        $query = new Query(self::TERM, self::DOMAIN_ID, new SortParameter());
        $query->setLanguage(self::ENGLISH);

        $esQuery = $this->queryFactory->create($query);

        $this->assertInstanceOf(FallbackQuery::class, $esQuery);
    }

    public function testItCreatesTheFallbackQueryFrenchForTheFrenchLanguage()
    {
        $query = new Query(self::TERM, self::DOMAIN_ID, new SortParameter());
        $query->setLanguage(self::FRENCH);
        $esQuery = $this->queryFactory->create($query);

        $this->assertInstanceOf(FallbackQuery::class, $esQuery);
    }
}
