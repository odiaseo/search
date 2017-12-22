<?php

namespace MapleSyrupGroup\Search\Services\Importer\Documents\Hydrators;

use Elastica\Type;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger;
use MapleSyrupGroup\Search\Services\Importer\Documents\DocumentsFromModel;
use MapleSyrupGroup\Search\Services\Importer\Documents\Validators\DocumentHydrationValidator;
use MapleSyrupGroup\Search\Services\IndexBuilder\Index\SearchIndex;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class ElasticaDocumentHydratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ElasticaDocumentHydrator
     */
    private $hydrator;

    private $type = 'static';

    public function setUp()
    {
        $validator = $this->prophesize(DocumentHydrationValidator::class);
        $logger    = $this->prophesize(BusinessEventLogger::class);
        $model     = $this->prophesize(DocumentsFromModel::class);
        $logger->log(Argument::cetera())->shouldBeCalled();

        $hydra = new ElasticaDocumentHydrator(
            1, $this->type, $validator->reveal(), $logger->reveal(), $model->reveal()
        );

        $this->hydrator = $hydra;
    }

    public function testSearchDocumentCanBeHydrated()
    {
        $index = $this->prophesize(SearchIndex::class);
        $index->getType($this->type)->willReturn(new Type($index->reveal(), 'name'));
        $index->refresh()->shouldBeCalled();
        $index->getName()->shouldBeCalled();

        $output = $this->prophesize(LoggerInterface::class);

        $result = $this->hydrator->hydrate($index->reveal(), $output->reveal());
        $this->assertNull($result);
    }
}
