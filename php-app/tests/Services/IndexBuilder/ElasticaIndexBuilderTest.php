<?php

namespace MapleSyrupGroup\Search\Services\IndexBuilder;

use Elastica\Response;
use Elastica\Status;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger;
use MapleSyrupGroup\Search\Services\Client\SearchClient;
use MapleSyrupGroup\Search\Services\Importer\Documents\Hydrators\ElasticaDocumentHydrator;
use MapleSyrupGroup\Search\Services\Importer\Types\StaticTypeBuilder;
use MapleSyrupGroup\Search\Services\IndexBuilder\Index\Index;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

/**
 * @group tracker
 */
class ElasticaIndexBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $indexName = 'test';

    public function testSearchIndexCanBeBuildSuccessfully()
    {
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->debug(Argument::cetera())->shouldBeCalled();
        $logger->info(Argument::cetera())->shouldBeCalled();
        $logger->info('Done!')->shouldBeCalled();
        $this->getIndexBuilder()->build($this->indexName, $logger->reveal());
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Services\Importer\Exceptions\IndexBuildException
     */
    public function testThatExceptionIsThrownWhenNewExistDoesNotExists()
    {
        $logger = $this->prophesize(LoggerInterface::class);
        $this->getIndexBuilder(['_index' => 'invalid'])->build($this->indexName, $logger->reveal());
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Services\Importer\Exceptions\IndexBuildException
     */
    public function testSearchIndexBuildExceptionIsHandled()
    {
        $client         = $this->prophesize(SearchClient::class);
        $logger         = $this->prophesize(LoggerInterface::class);
        $businessLogger = $this->prophesize(BusinessEventLogger::class);
        $index          = $this->prophesize(Index::class);

        $index->create([])->shouldBeCalled();
        $index->delete()->shouldBeCalled();
        $index->getClient()->willReturn($client->reveal());
        $client->getIndex(Argument::any())->willReturn($index->reveal());
        $client->getStatus(Argument::cetera())->willThrow(new \Exception());

        $builder = new ElasticaIndexBuilder($client->reveal(), [], [], [], $businessLogger->reveal());

        $builder->build($this->indexName, $logger->reveal());
    }

    private function getIndexBuilder($checkData = null)
    {
        $config    = [];
        $suffix    = time();
        $indexName = $this->indexName . '_' . $suffix;
        $client    = $this->prophesize(SearchClient::class);
        $index     = $this->prophesize(Index::class);
        $status    = $this->prophesize(Status::class);
        $response  = $this->prophesize(Response::class);

        $status->getIndicesWithAlias(Argument::any())->willReturn([$index->reveal()]);

        $index->create($config)->willReturn(null);
        $index->delete()->willReturn(null);
        $index->getName()->willReturn($indexName);
        $index->getClient()->willReturn($client->reveal());
        $index->request(Argument::cetera())->willReturn($response);

        if (!$checkData) {
            $index->addAlias($this->indexName, true)->shouldBeCalled();
            $checkData = ['_index' => $indexName ];
        }

        $response->getData()->willReturn($checkData);
        $index->count()->willReturn(1);

        $client->getIndex(Argument::any())->willReturn($index->reveal());
        $client->getStatus()->willReturn($status);

        $logger    = $this->prophesize(BusinessEventLogger::class)->reveal();
        $providers = [$this->prophesize(ElasticaDocumentHydrator::class)->reveal()];
        $builders  = [
            $this->prophesize(StaticTypeBuilder::class)->reveal(),
        ];

        return new ElasticaIndexBuilder($client->reveal(), $config, $builders, $providers, $logger, $suffix);
    }
}
