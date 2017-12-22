<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch;

use MapleSyrupGroup\Search\Services\Merchants\DomainQuery;

interface IndexNameFactory
{
    /**
     * @param DomainQuery $query
     *
     * @return string
     */
    public function getIndexName(DomainQuery $query);
}
