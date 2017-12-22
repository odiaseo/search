<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch;

use Elastica\Client as Elastica;
use MapleSyrupGroup\Search\Services\Client\ElasticaSearchClient;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\Search;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Stubs\ElasticsearchQueryStub;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Stubs\TestIndexer;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @group integration
 */
class ElasticsearchSearchTest extends \PHPUnit_Framework_TestCase
{
    const INDEX_NAME = 'test_elasticsearch_search';

    const DOMAIN_ID = 1;

    const SEARCH_TERM = 'foo';

    /**
     * @var ElasticsearchSearch
     */
    private $search;

    /**
     * @var QueryFactory|ObjectProphecy
     */
    private $queryFactory;

    /**
     * @var IndexNameFactory|ObjectProphecy
     */
    private $indexNameFactory;

    /**
     * @var Elastica
     */
    private $elastica;

    protected function setUp()
    {
        $this->queryFactory = $this->prophesize(QueryFactory::class);
        $this->indexNameFactory = $this->prophesize(IndexNameFactory::class);
        $this->indexNameFactory->getIndexName(Argument::type(Query::class))->willReturn(self::INDEX_NAME);

        $this->elastica = new ElasticaSearchClient([
            'host' => getenv('ELASTICSEARCH_HOST') !== false ? getenv('ELASTICSEARCH_HOST') : '127.0.0.1',
            'port' => getenv('ELASTICSEARCH_PORT') !== false ? getenv('ELASTICSEARCH_PORT') : 9200
        ]);

        $this->search = new ElasticsearchSearch(
            $this->queryFactory->reveal(),
            $this->indexNameFactory->reveal(),
            $this->elastica
        );

        $this->createIndex();
    }

    public function testItIsASearch()
    {
        $this->assertInstanceOf(Search::class, $this->search);
    }

    public function testItCallsElasticsearchWithTheBuiltQuery()
    {
        $query = new Query(self::SEARCH_TERM, self::DOMAIN_ID, new SortParameter('relevance', 'asc'));
        $elasticsearchQuery = ElasticsearchQueryStub::fromQuery($query);

        $this->queryFactory->create($query)->willReturn($elasticsearchQuery);

        $resultSet = $this->search->search($query);

        $this->assertInternalType('array', $resultSet);
        $this->assertArrayHasKey('hits', $resultSet);
        $this->assertArrayHasKey('total', $resultSet['hits']);
        $this->assertSame(1, $resultSet['hits']['total']);
    }

    private function createIndex()
    {
        $indexer = new TestIndexer($this->elastica, ElasticsearchQuery::DOMAIN_ID_FIELD);
        $indexer->index(self::INDEX_NAME, self::DOMAIN_ID);
    }
}
