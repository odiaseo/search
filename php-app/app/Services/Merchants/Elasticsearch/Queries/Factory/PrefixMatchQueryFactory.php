<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\MerchantPrefixMatchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\QueryFactory;
use MapleSyrupGroup\Search\Services\Merchants\Query;

class PrefixMatchQueryFactory implements QueryFactory
{
    use MerchantQueryFilterTrait;

    /**
     * @param Query $query
     *
     * @return ElasticsearchQuery
     */
    public function create(Query $query)
    {
        return MerchantPrefixMatchQuery::fromQuery($query, $this->getFilter());
    }
}
