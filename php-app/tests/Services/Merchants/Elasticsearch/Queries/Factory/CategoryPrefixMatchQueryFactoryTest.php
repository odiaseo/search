<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\CategoryPrefixMatchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;

class CategoryPrefixMatchQueryFactoryTest extends QueryFactoryTestCase
{
    protected function createQueryFactory()
    {
        return new CategoryPrefixMatchQueryFactory();
    }

    public function testItCreatesTheCategoryExactMatchQuery()
    {
        $query   = new Query(self::TERM, self::DOMAIN_ID, new SortParameter());
        $esQuery = $this->queryFactory->create($query);

        $this->assertInstanceOf(CategoryPrefixMatchQuery::class, $esQuery);
    }

    public function testItCreatesTheCategoryExactMatchQueryWithSortOrder()
    {
        $query   = new Query(self::TERM, self::DOMAIN_ID, new SortParameter('relevance', 'asc'));
        $esQuery = $this->queryFactory->create($query);

        $this->assertInstanceOf(CategoryPrefixMatchQuery::class, $esQuery);
    }
}
