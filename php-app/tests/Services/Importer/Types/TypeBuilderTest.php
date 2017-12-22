<?php

namespace MapleSyrupGroup\Search\Services\Importer\Types;

use MapleSyrupGroup\Search\Models\SearchableModel;
use MapleSyrupGroup\Search\Services\Client\ElasticaSearchClient;
use MapleSyrupGroup\Search\Services\Importer\IndexMapping\ElasticaIndexMapping;
use MapleSyrupGroup\Search\Services\IndexBuilder\Index\SearchIndex;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class TypeBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testThatClassTypeBuilderCanPopulateTheSearchIndexWithMappingInformation()
    {
        $searchModel = $this->prophesize(SearchableModel::class);
        $mapping     = $this->prophesize(ElasticaIndexMapping::class);
        $logger      = $this->prophesize(LoggerInterface::class);

        $searchModel->getMappingProperties()->willReturn([]);
        $mapping->setType(Argument::any())->willReturn($mapping->reveal());
        $mapping->setProperties([])->willReturn($mapping->reveal());
        $mapping->send()->shouldBeCalled();

        $logger->info(Argument::any())->shouldBeCalled();

        $classType = new ClassTypeBuilder('sample', $searchModel->reveal(), $mapping->reveal());

        $classType->build(new SearchIndex(new ElasticaSearchClient([]), 'test'), $logger->reveal());
    }

    public function testThatStaticTypeBuilderCanPopulateTheSearchIndexWithMappingInformation()
    {
        $config      = [];
        $searchModel = $this->prophesize(SearchableModel::class);
        $mapping     = $this->prophesize(ElasticaIndexMapping::class);
        $logger      = $this->prophesize(LoggerInterface::class);

        $searchModel->getMappingProperties()->willReturn([]);
        $mapping->setType(Argument::any())->willReturn($mapping->reveal());
        $mapping->setProperties([])->willReturn($mapping->reveal());
        $mapping->send()->shouldBeCalled();

        $logger->info(Argument::any())->shouldBeCalled();

        $staticType = new StaticTypeBuilder('sample', $config, $mapping->reveal());

        $staticType->build(new SearchIndex(new ElasticaSearchClient([]), 'test'), $logger->reveal());
    }
}
