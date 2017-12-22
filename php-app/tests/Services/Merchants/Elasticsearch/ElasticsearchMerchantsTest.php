<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch;

use Elastica\Client as Elastica;
use MapleSyrupGroup\Search\Services\Client\ElasticaSearchClient;
use MapleSyrupGroup\Search\Services\Importer\Exceptions\IndexDefinitionException;
use MapleSyrupGroup\Search\Services\Merchants\DomainQuery;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\LinkQuery;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Merchant;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Merchants;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @group integration
 * @group elasticsearch-index
 *
 * Until we can easily build the index during the tests for data we set up
 * this test relies on previously built index for the servicetest environment.
 */
class ElasticsearchMerchantsTest extends \PHPUnit_Framework_TestCase
{
    const DOMAIN_ID = 200;

    const UNKNOWN_DOMAIN_ID = 2;

    const MATCHING_MERCHANT_ID = 14;

    const MATCHING_LINK = 'http://www.rugbycenter.fr/foo/bar';

    const NOT_MATCHING_LINK = 'http://store.rugbycenter.fr/foo/bar';

    /**
     * @var ElasticsearchMerchants
     */
    private $merchants;

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
        $this->indexNameFactory = $this->prophesize(IndexNameFactory::class);
        $this->indexNameFactory->getIndexName(Argument::type(DomainQuery::class))->willReturn($this->getIndexName());
        $this->elastica = new ElasticaSearchClient([
            'host' => getenv('ELASTICSEARCH_HOST') !== false ? getenv('ELASTICSEARCH_HOST') : '127.0.0.1',
            'port' => getenv('ELASTICSEARCH_PORT') !== false ? getenv('ELASTICSEARCH_PORT') : 9200,
        ]);

        $this->merchants = new ElasticsearchMerchants($this->elastica, $this->indexNameFactory->reveal());
    }

    public function testItIsAMerchantsRepository()
    {
        $this->assertInstanceOf(Merchants::class, $this->merchants);
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Services\Merchants\FindByUrl\NoMerchantFoundException
     */
    public function testItFiltersOutNotMatchingSubdomains()
    {
        $this->merchants->getByLink(new LinkQuery(self::NOT_MATCHING_LINK, self::DOMAIN_ID));
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Services\Merchants\FindByUrl\NoMerchantFoundException
     */
    public function testItFiltersOutNotMatchingDomainIds()
    {
        $this->merchants->getByLink(new LinkQuery(self::MATCHING_LINK, self::UNKNOWN_DOMAIN_ID));
    }

    public function testSearchResultsAreMappedToMerchants()
    {
        $data      = [
            'hits' => [
                'hits' => [
                    [
                        '_source' => [
                            'id'          => 1,
                            'name'        => 'Argos',
                            'url_name'    => 'argos',
                            'description' => 'argos stores',
                            'links'       => [],
                        ],
                    ],
                    [
                        '_source' => [
                            'id'          => 2,
                            'name'        => 'Tesco',
                            'url_name'    => 'tesco',
                            'description' => 'argos stores',
                            'links'       => [
                                'http://www.tesco.com'
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $merchants = $this->merchants->mapResultToMerchants($data);
        $this->assertContainsOnlyInstancesOf(Merchant::class, $merchants);
    }

    private function getIndexName()
    {
        if (getenv('ELASTICSEARCH_INDEX_NAME') === false) {
            throw new IndexDefinitionException('Unable to locate elastic search index for shoop');
        }
        $indexName = sprintf('testing_%s_200', str_replace('testing_', '', getenv('ELASTICSEARCH_INDEX_NAME')));

        return $indexName;
    }
}
