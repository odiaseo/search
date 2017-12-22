<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\FallbackQuery;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\QueryFactory;
use MapleSyrupGroup\Search\Services\Merchants\Query;

class FallbackQueryFactory implements QueryFactory
{
    use MerchantQueryFilterTrait;

    /**
     * @param Query $query
     *
     * @return ElasticsearchQuery
     */
    public function create(Query $query)
    {
        return FallbackQuery::fromQuery($query, $this->getFilter());
    }
}
