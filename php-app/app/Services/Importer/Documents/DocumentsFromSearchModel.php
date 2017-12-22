<?php

namespace MapleSyrupGroup\Search\Services\Importer\Documents;

use Elastica\Document;
use Elastica\Index;
use Elastica\Type;
use MapleSyrupGroup\Search\Models\SearchableModel;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\DocumentInsert\MerchantCountChanged;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\DocumentInsert\UnexpectedApiResponse;
use MapleSyrupGroup\Search\Services\Importer\Exceptions\UnexpectedApiResponseException;
use Psr\Log\LoggerInterface;

/**
 * Inserts documents from the model into an Index.
 */
class DocumentsFromSearchModel implements DocumentsFromModel
{
    /**
     * @var SearchableModel
     */
    private $searchableModel;

    /**
     * Number of records to process per request.
     * 
     * @var int
     */
    private $bufferSize;

    /**
     * @var BusinessEventLogger
     */
    private $businessEventLogger;

    /**
     * DocumentsFromModelImplementation constructor.
     *
     * @param SearchableModel     $model
     * @param BusinessEventLogger $logger
     * @param int                 $bufferSize
     */
    public function __construct(SearchableModel $model, BusinessEventLogger $logger, $bufferSize)
    {
        $this->setSearchableModel($model);
        $this->setBusinessEventLogger($logger);
        $this->setBufferSize($bufferSize);
    }

    /**
     * Insert the documents from this model into the index.
     *
     * Returns the number of documents we should have inserted
     *
     * @param LoggerInterface $output
     * @param Type            $type
     *
     * @return int
     */
    public function insert(LoggerInterface $output, Type $type)
    {
        $output->info(
            sprintf(
                'Inserting documents into %s/%s from %s',
                $type->getName(),
                $type->getIndex()->getName(),
                get_class($this->getSearchableModel())
            )
        );

        $entities = $this->getSearchableModel()->all(1, $this->getBufferSize());
        $total    = $entities['total'];
        $output->info("\t$total documents to process");

        $pagesToRequest = range(1, $entities['pages']);

        $this->processRequestPerPage($type->getIndex(), $type, $output, $pagesToRequest, $total);

        return $total;
    }

    /**
     * @param Index           $index
     * @param Type            $type
     * @param LoggerInterface $output
     * @param array           $pagesToRequest
     * @param int             $total
     */
    private function processRequestPerPage(
        Index $index,
        Type $type,
        LoggerInterface $output,
        array $pagesToRequest,
        $total
    ) {
        $totalRemaining = $total;
        foreach ($pagesToRequest as $page) {
            $documents = $this->buildDocuments($this->getResultPerPage($index, $type, $page, $total));

            $type->addDocuments($documents);

            $totalRemaining -= count($documents);
            $output->info("\t$totalRemaining documents remaining");
        }
    }

    /**
     * @param array $result
     * @param Index $index
     * @param Type  $type
     * @param int   $total
     */
    private function validateTotalDocuments(array $result, Index $index, Type $type, $total)
    {
        if ($result['total'] !== $total) {
            $this->getBusinessEventLogger()->log(
                new MerchantCountChanged($index->getName(), $type->getName(), $total, $result['total'])
            );

            $message = 'Merchants have changed before import finished';
            $message .= ", merchant count changed from {$total} to {$result['total']}";

            throw new UnexpectedApiResponseException($message);
        }
    }

    /**
     * @param array $result
     * @param Index $index
     * @param Type  $type
     */
    private function validResultTotalExists(array $result, Index $index, Type $type)
    {
        if (!isset($result['total']) || !isset($result['result'])) {
            $this->getBusinessEventLogger()->log(
                new UnexpectedApiResponse($result, $index->getName(), $type->getName())
            );

            throw new UnexpectedApiResponseException('Unexpected response from API: ' . json_encode($result));
        }
    }

    /**
     * @param Index $index
     * @param Type  $type
     * @param int   $page
     * @param int   $total
     *
     * @return array
     */
    private function getResultPerPage($index, $type, $page, $total)
    {
        $result = $this->getSearchableModel()->all($page, $this->getBufferSize());

        $this->validResultTotalExists($result, $index, $type);
        $this->validateTotalDocuments($result, $index, $type, $total);

        return $result;
    }

    /**
     * @param array $result
     *
     * @return Document[]
     */
    private function buildDocuments(array $result)
    {
        $documents = [];

        foreach ($result['result'] as $entity) {
            $documents[] = new Document($entity['id'], $this->getSearchableModel()->toDocumentArray($entity));
        }

        return $documents;
    }

    /**
     * @return SearchableModel
     */
    public function getSearchableModel()
    {
        return $this->searchableModel;
    }

    /**
     * @param SearchableModel $searchableModel
     */
    private function setSearchableModel($searchableModel)
    {
        $this->searchableModel = $searchableModel;
    }

    /**
     * @return int
     */
    public function getBufferSize()
    {
        return $this->bufferSize;
    }

    /**
     * @param int $bufferSize
     */
    private function setBufferSize($bufferSize)
    {
        $this->bufferSize = $bufferSize;
    }

    /**
     * @return BusinessEventLogger
     */
    public function getBusinessEventLogger()
    {
        return $this->businessEventLogger;
    }

    /**
     * @param BusinessEventLogger $businessEventLogger
     */
    private function setBusinessEventLogger($businessEventLogger)
    {
        $this->businessEventLogger = $businessEventLogger;
    }
}
