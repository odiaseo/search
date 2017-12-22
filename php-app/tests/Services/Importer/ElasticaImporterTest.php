<?php

namespace MapleSyrupGroup\Search\Services\Importer;

use Exception;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger;
use MapleSyrupGroup\Search\Services\IndexBuilder\ElasticaIndexBuilder;
use MapleSyrupGroup\Search\Services\IndexStatusTracker\IndexStatusTracker;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class ElasticaImporterTest extends \PHPUnit_Framework_TestCase
{
    private $indexName = 'test_index';

    private $domain = 1;

    public function testIndexCanImportDataSuccessfully()
    {
        $builders      = [
            $this->indexName . '_' . $this->domain => $this->prophesize(ElasticaIndexBuilder::class)->reveal(),
        ];
        $status        = null;
        $retries       = 3;
        $statusTracker = $this->prophesize(IndexStatusTracker::class);
        $logger        = $this->prophesize(BusinessEventLogger::class);
        $output        = $this->prophesize(LoggerInterface::class);

        $statusTracker->isRunning($this->domain, $status)->shouldBeCalled();
        $statusTracker->lock($this->domain, [], $status)->shouldBeCalled();
        $statusTracker->unlock($this->domain, $status)->shouldBeCalled();

        $importer = new ElasticaImporter($builders, $statusTracker->reveal(), $logger->reveal(), $retries);
        $result   = $importer->doImport($this->indexName, $this->domain, $output->reveal(), $status);
        $this->assertNull($result);
    }

    public function testImporterUnableToMoreThanOneInstance()
    {
        $retries       = 3;
        $status        = null;
        $statusTracker = $this->prophesize(IndexStatusTracker::class);
        $logger        = $this->prophesize(BusinessEventLogger::class);
        $output        = $this->prophesize(LoggerInterface::class);

        $statusTracker->isRunning($this->domain, $status)->willReturn(true);
        $statusTracker->getStatus($this->domain, $status)->willReturn('{}');

        $importer = new ElasticaImporter([], $statusTracker->reveal(), $logger->reveal(), $retries);
        $result   = $importer->doImport($this->indexName, $this->domain, $output->reveal(), $status);
        $this->assertFalse($result);
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Services\Importer\Exceptions\IndexDefinitionException
     */
    public function testExceptionIsThrownWithNoConfiguredIndexBuilder()
    {
        $status        = null;
        $retries       = 3;
        $statusTracker = $this->prophesize(IndexStatusTracker::class);
        $logger        = $this->prophesize(BusinessEventLogger::class);
        $output        = $this->prophesize(LoggerInterface::class);
        $builders      = [
            $this->indexName . '_230' => $this->prophesize(ElasticaIndexBuilder::class)->reveal(),
        ];
        $statusTracker->isRunning($this->domain, $status)->shouldBeCalled();
        $statusTracker->lock($this->domain, [], $status)->shouldBeCalled();

        $importer = new ElasticaImporter($builders, $statusTracker->reveal(), $logger->reveal(), $retries, 0);
        $importer->doImport($this->indexName, $this->domain, $output->reveal(), $status);
    }

    public function testThanIndexBuilderExceptionIsHandled()
    {
        $status        = null;
        $retries       = 1;
        $domainId      = null;
        $builder       = $this->prophesize(ElasticaIndexBuilder::class);
        $statusTracker = $this->prophesize(IndexStatusTracker::class);
        $logger        = $this->prophesize(BusinessEventLogger::class);
        $output        = $this->prophesize(LoggerInterface::class);

        $builder->build(Argument::cetera())->willThrow(new Exception());

        $builders = [
            $this->indexName . '_' . $this->domain => $builder->reveal(),
        ];

        $statusTracker->isRunning($domainId, $status)->shouldBeCalled();
        $statusTracker->lock($domainId, [], $status)->shouldBeCalled();
        $statusTracker->unlock($domainId, $status)->shouldBeCalled();

        $importer = new ElasticaImporter($builders, $statusTracker->reveal(), $logger->reveal(), $retries, 0);

        $this->assertEquals($retries, $importer->getRetryCount());
        $this->assertNotEquals(0, $importer->getRetryCount());

        $result = $importer->doImport($this->indexName, $domainId, $output->reveal(), $status);

        $this->assertNull($result);
        $this->assertEquals(0, $importer->getRetryCount());
    }
}
