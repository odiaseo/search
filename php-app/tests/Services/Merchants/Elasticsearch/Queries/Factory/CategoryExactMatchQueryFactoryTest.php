<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\CategoryExactMatchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;

class CategoryExactMatchQueryFactoryTest extends QueryFactoryTestCase
{
    protected function createQueryFactory()
    {
        return new CategoryExactMatchQueryFactory();
    }

    public function testItCreatesTheCategoryExactMatchQuery()
    {
        $query   = new Query(self::TERM, self::DOMAIN_ID, new SortParameter());
        $esQuery = $this->queryFactory->create($query);

        $this->assertInstanceOf(CategoryExactMatchQuery::class, $esQuery);
    }

    public function testItCreatesTheCategoryExactMatchQueryWithUserDefinedSortOrder()
    {
        $query   = new Query(self::TERM, self::DOMAIN_ID, new SortParameter('relevance', 'desc'));
        $esQuery = $this->queryFactory->create($query);

        $this->assertInstanceOf(CategoryExactMatchQuery::class, $esQuery);
    }
}
