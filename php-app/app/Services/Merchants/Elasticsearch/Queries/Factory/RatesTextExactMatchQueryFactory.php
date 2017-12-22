<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\RatesTextExactMatchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\QueryFactory;
use MapleSyrupGroup\Search\Services\Merchants\Query;

class RatesTextExactMatchQueryFactory implements QueryFactory
{
    /**
     * @param Query $query
     *
     * @return ElasticsearchQuery
     */
    public function create(Query $query)
    {
        return RatesTextExactMatchQuery::fromQuery($query);
    }
}
