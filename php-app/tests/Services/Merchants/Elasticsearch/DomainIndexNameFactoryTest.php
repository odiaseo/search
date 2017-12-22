<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch;

use MapleSyrupGroup\Search\Services\Merchants\DomainQuery;

class DomainIndexNameFactoryTest extends \PHPUnit_Framework_TestCase
{
    const INDEX_PREFIX = 'my_elasticsearch_index';

    /**
     * @var DomainIndexNameFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = new DomainIndexNameFactory(self::INDEX_PREFIX);
    }

    public function testItIsAnIndexNameFactory()
    {
        $this->assertInstanceOf(IndexNameFactory::class, $this->factory);
    }

    public function testItCreatesAnIndexNameBasedOnDomainIdAndPrefix()
    {
        $domainId = 200;
        $query = $this->prophesize(DomainQuery::class);
        $query->getDomainId()->willReturn($domainId);

        $indexName = $this->factory->getIndexName($query->reveal());

        $this->assertSame(self::INDEX_PREFIX.'_'.$domainId, $indexName);
    }
}
