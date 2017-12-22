<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch;

use MapleSyrupGroup\Search\Services\Client\SearchClient;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\WildcardLinkQuery;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\LinkQuery;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Merchant;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Merchants;

/**
 * Elasticsearch implementation of the merchants repostiory.
 */
class ElasticsearchMerchants implements Merchants
{
    /**
     * @var SearchClient
     */
    private $elastica;

    /**
     * @var IndexNameFactory
     */
    private $indexNameFactory;

    /**
     * @param SearchClient     $elastica
     * @param IndexNameFactory $indexNameFactory
     */
    public function __construct(SearchClient $elastica, IndexNameFactory $indexNameFactory)
    {
        $this->setElastica($elastica);
        $this->setIndexNameFactory($indexNameFactory);
    }

    /**
     * @param LinkQuery $query
     *
     * @return Merchant
     */
    public function getByLink(LinkQuery $query)
    {
        $eqQuery = WildcardLinkQuery::fromLinkQuery($query);
        $path    = sprintf('%s/%s/_search', $this->getIndexNameFactory()->getIndexName($query), $eqQuery->getType());

        $data = (array) $this->getElastica()->request($path, 'GET', $eqQuery->toArray())->getData();

        $merchants = $this->mapResultToMerchants($data);

        $filter = new LinkMerchantFilter($merchants);

        return $filter($query->getLink());
    }

    /**
     * @param array $data
     *
     * @return Merchant[]
     */
    public function mapResultToMerchants(array $data)
    {
        return array_map(
            function (array $hit) {
                return new Merchant(
                    $hit['_source']['id'],
                    $hit['_source']['name'],
                    $hit['_source']['url_name'],
                    $hit['_source']['description'],
                    $hit['_source']['links']
                );
            },
            $data['hits']['hits']
        );
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
     * @return ElasticsearchMerchants
     */
    private function setElastica($elastica)
    {
        $this->elastica = $elastica;

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
     * @return ElasticsearchMerchants
     */
    private function setIndexNameFactory($indexNameFactory)
    {
        $this->indexNameFactory = $indexNameFactory;

        return $this;
    }
}
