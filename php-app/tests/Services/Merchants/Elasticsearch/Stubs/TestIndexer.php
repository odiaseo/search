<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Stubs;

use Elastica\Document;
use Elastica\Index;
use Elastica\Type;
use Elastica\Type\Mapping;
use MapleSyrupGroup\Search\Services\Client\SearchClient;

class TestIndexer
{
    const INDEX_TYPE = ElasticsearchQueryStub::TYPE;

    /**
     * @var SearchClient
     */
    private $elastica;

    /**
     * @var string
     */
    private $domainIdField;

    /**
     * @param SearchClient $elastica
     * @param string $domainIdField
     */
    public function __construct(SearchClient $elastica, $domainIdField)
    {
        $this->elastica      = $elastica;
        $this->domainIdField = $domainIdField;
    }

    /**
     * @param string $indexName
     * @param int $domainId
     */
    public function index($indexName, $domainId)
    {
        $elasticaIndex = $this->createIndex($indexName);
        $elasticaType  = $elasticaIndex->getType(self::INDEX_TYPE);
        $this->createMapping($elasticaType);
        $this->indexDocuments($elasticaType, $domainId);
    }

    /**
     * @param string $indexName
     *
     * @return Index
     */
    public function createIndex($indexName)
    {
        $elasticaIndex = $this->elastica->getIndex($indexName);
        $elasticaIndex->create(
            [
                'analysis' => [
                    'analyzer' => [
                        'indexAnalyzer'  => [
                            'type'      => 'custom',
                            'tokenizer' => 'standard',
                            'filter'    => ['lowercase', 'mySnowball'],
                        ],
                        'searchAnalyzer' => [
                            'type'      => 'custom',
                            'tokenizer' => 'standard',
                            'filter'    => ['standard', 'lowercase', 'mySnowball'],
                        ],
                    ],
                    'filter'   => ['mySnowball' => ['type' => 'snowball', 'language' => 'English']],
                ],
            ],
            true
        );

        return $elasticaIndex;
    }

    /**
     * @param Type $elasticaType
     */
    public function createMapping($elasticaType)
    {
        $mapping = new Mapping();
        $mapping->setType($elasticaType);
        $mapping->setProperties(
            [
                'id'                 => ['type' => 'integer', 'include_in_all' => false],
                'message'            => ['type' => 'string', 'include_in_all' => true],
                $this->domainIdField => ['type' => 'integer', 'include_in_all' => true],
                '_boost'             => ['type' => 'float', 'include_in_all' => false],
            ]
        );
        $mapping->send();
    }

    /**
     * @param Type $elasticaType
     * @param int $domainId
     */
    public function indexDocuments($elasticaType, $domainId)
    {
        $elasticaType->addDocument(
            new Document(
                1, ['id' => 1, 'message' => 'Foo bar baz.', $this->domainIdField => $domainId, '_boost' => 1.0]
            )
        );
        $elasticaType->addDocument(
            new Document(
                2,
                [
                    'id'                 => 2,
                    'message'            => 'Lorem ipsum dolor sit amet.',
                    $this->domainIdField => $domainId,
                    '_boost'             => 1.0,
                ]
            )
        );
        $elasticaType->getIndex()->refresh();
    }
}
