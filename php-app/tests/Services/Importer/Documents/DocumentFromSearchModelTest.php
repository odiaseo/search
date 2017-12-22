<?php

namespace MapleSyrupGroup\Search\Services\Importer\Documents;

use Elastica\Index;
use Elastica\Response;
use Elastica\Type;
use MapleSyrupGroup\Search\Models\SearchableModel;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger;
use MapleSyrupGroup\Search\Services\Client\ElasticaSearchClient;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class DocumentFromSearchModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $indexName = 'test_index';

    public function testDocumentsCanBeCreatedFromSearchModelData()
    {
        $total = 2;
        $data  = [
            'pages'  => 1,
            'total'  => $total,
            'result' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ];

        $logger = $this->prophesize(LoggerInterface::class);
        $client = $this->prophesize(ElasticaSearchClient::class);
        $client->request(Argument::cetera())->willReturn(new Response([]));
        $client->addDocuments(Argument::cetera())->shouldBeCalled();

        $type        = new Type(new Index($client->reveal(), $this->indexName), $this->indexName);
        $searchModel = $this->prophesize(SearchableModel::class);
        $searchModel->all(Argument::cetera())->willReturn($data);
        $searchModel->toDocumentArray(Argument::cetera())->willReturn([]);
        $eventLogger = $this->prophesize(BusinessEventLogger::class);

        $model  = new DocumentsFromSearchModel($searchModel->reveal(), $eventLogger->reveal(), 100);
        $result = $model->insert($logger->reveal(), $type);

        $this->assertSame($total, $result);
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Services\Importer\Exceptions\UnexpectedApiResponseException
     */
    public function testExceptionIsThrownWithMissingResultTotal()
    {
        $buffer = 100;
        $data1  = [
            'pages'  => 2,
            'total'  => 2,
            'result' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ];

        $data2 = [
            'pages'  => 2,
            'result' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ];

        $logger = $this->prophesize(LoggerInterface::class);
        $client = $this->prophesize(ElasticaSearchClient::class);
        $client->request(Argument::cetera())->willReturn(new Response([]));
        $client->addDocuments(Argument::cetera())->shouldBeCalled();

        $type        = new Type(new Index($client->reveal(), $this->indexName), $this->indexName);
        $searchModel = $this->prophesize(SearchableModel::class);
        $searchModel->all(1, $buffer)->willReturn($data1);
        $searchModel->all(Argument::cetera())->willReturn($data2);
        $searchModel->toDocumentArray(Argument::cetera())->willReturn([]);
        $eventLogger = $this->prophesize(BusinessEventLogger::class);

        $model = new DocumentsFromSearchModel($searchModel->reveal(), $eventLogger->reveal(), $buffer);
        $model->insert($logger->reveal(), $type);
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Services\Importer\Exceptions\UnexpectedApiResponseException
     */
    public function testExceptionIsThrownWithMismatchedDocumentTotal()
    {
        $buffer = 100;
        $data1  = [
            'pages'  => 2,
            'total'  => 2,
            'result' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ];

        $data2 = [
            'pages'  => 2,
            'total'  => 3,
            'result' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ];

        $logger = $this->prophesize(LoggerInterface::class);
        $client = $this->prophesize(ElasticaSearchClient::class);
        $client->request(Argument::cetera())->willReturn(new Response([]));
        $client->addDocuments(Argument::cetera())->shouldBeCalled();

        $type        = new Type(new Index($client->reveal(), $this->indexName), $this->indexName);
        $searchModel = $this->prophesize(SearchableModel::class);
        $searchModel->all(1, $buffer)->willReturn($data1);
        $searchModel->all(Argument::cetera())->willReturn($data2);
        $searchModel->toDocumentArray(Argument::cetera())->willReturn([]);
        $eventLogger = $this->prophesize(BusinessEventLogger::class);

        $model = new DocumentsFromSearchModel($searchModel->reveal(), $eventLogger->reveal(), $buffer);
        $model->insert($logger->reveal(), $type);
    }
}
