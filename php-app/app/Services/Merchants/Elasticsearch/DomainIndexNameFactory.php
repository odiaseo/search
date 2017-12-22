<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch;

use MapleSyrupGroup\Search\Services\Merchants\DomainQuery;

class DomainIndexNameFactory implements IndexNameFactory
{
    /**
     * @var string
     */
    private $indexNamePrefix;

    /**
     * @param string $indexNamePrefix
     */
    public function __construct($indexNamePrefix)
    {
        $this->indexNamePrefix = $indexNamePrefix;
    }

    /**
     * @param DomainQuery $query
     *
     * @return string
     */
    public function getIndexName(DomainQuery $query)
    {
        return sprintf('%s_%s', $this->indexNamePrefix, $query->getDomainId());
    }
}
