<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch;

use MapleSyrupGroup\Search\Services\Merchants\Query;

interface QueryFactory
{
    /**
     * @param Query $query
     *
     * @return ElasticsearchQuery
     */
    public function create(Query $query);
}
