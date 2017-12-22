<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch;

use MapleSyrupGroup\Search\Services\Client\SearchClient;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\Search;

class ElasticsearchSearch implements Search
{
    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var IndexNameFactory
     */
    private $indexNameFactory;

    /**
     * @var SearchClient
     */
    private $elastica;

    /**
     * @param QueryFactory     $queryFactory
     * @param IndexNameFactory $nameFactory
     * @param SearchClient     $client
     */
    public function __construct(QueryFactory $queryFactory, IndexNameFactory $nameFactory, SearchClient $client)
    {
        $this->setElastica($client);
        $this->setIndexNameFactory($nameFactory);
        $this->setQueryFactory($queryFactory);
    }

    /**
     * @param Query $query
     *
     * @return array
     */
    public function search(Query $query)
    {
        $elasticsearchQuery = $this->getQueryFactory()->create($query);
        $path               = sprintf(
            '%s/%s/_search',
            $this->getIndexNameFactory()->getIndexName($query),
            $elasticsearchQuery->getType()
        );

        return $this->getElastica()->request($path, 'GET', $elasticsearchQuery->toArray())->getData();
    }

    /**
     * @return QueryFactory
     */
    public function getQueryFactory()
    {
        return $this->queryFactory;
    }

    /**
     * @param QueryFactory $queryFactory
     *
     * @return ElasticsearchSearch
     */
    private function setQueryFactory($queryFactory)
    {
        $this->queryFactory = $queryFactory;

        return $this;
    }

    /**
     * @return IndexNameFactory
     */
    public function getIndexNameFactory()
    {
        return $this->indexNameFactory;
    }

    /**
     * @param IndexNameFactory $indexNameFactory
     *
     * @return ElasticsearchSearch
     */
    public function setIndexNameFactory($indexNameFactory)
    {
        $this->indexNameFactory = $indexNameFactory;

        return $this;
    }

    /**
     * @return SearchClient
     */
    public function getElastica()
    {
        return $this->elastica;
    }

    /**
     * @param SearchClient $elastica
     *
     * @return ElasticsearchSearch
     */
    public function setElastica($elastica)
    {
        $this->elastica = $elastica;

        return $this;
    }
}
