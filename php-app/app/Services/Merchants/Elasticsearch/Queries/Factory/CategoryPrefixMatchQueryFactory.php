<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\CategoryPrefixMatchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Query;

class CategoryPrefixMatchQueryFactory extends CategoryExactMatchQueryFactory
{
    /**
     * @param Query $query
     *
     * @return ElasticsearchQuery
     */
    public function create(Query $query)
    {
        return CategoryPrefixMatchQuery::fromQuery($query, $this->getFilter());
    }
}
