<?php

namespace MapleSyrupGroup\Search\Services\Importer\Documents\Validators;

use Elastica\Index;
use Elastica\Query;
use Elastica\Response;
use Elastica\Search;
use Elastica\Type;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger;
use MapleSyrupGroup\Search\Services\Client\ElasticaSearchClient;
use Prophecy\Argument;

class DocumentHydrationValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DocumentHydrationValidator
     */
    private $validator;

    private $indexName = 'test_index';

    private $client;

    public function setUp()
    {
        $client = $this->prophesize(ElasticaSearchClient::class);
        $client->request(Argument::cetera())->willReturn(new Response([]));

        $search       = new Search($client->reveal());
        $logger       = $this->prophesize(BusinessEventLogger::class);
        $this->client = $client;

        $this->validator = new DocumentHydrationValidator($search, $logger->reveal(), new Query(), new Query\Term());
    }

    public function testSearchDocumentCanBeValidatedWithoutException()
    {
        $type   = new Type(new Index($this->client->reveal(), $this->indexName), $this->indexName);
        $result = $this->validator->validate($type, 1, 0);

        $this->assertNull($result);
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Services\Importer\Exceptions\ImportValidationException
     */
    public function testSearchDocumentValidationThrowsExceptionWithMismatchTotal()
    {
        $type = new Type(new Index($this->client->reveal(), $this->indexName), $this->indexName);
        $this->validator->validate($type, 1, 1);
    }
}
