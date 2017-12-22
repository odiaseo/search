<?php

namespace MapleSyrupGroup\Search\Services\Importer\Documents\Hydrators;

use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger as Logger;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\DocumentInsert\Success;
use MapleSyrupGroup\Search\Services\Importer\Documents\DocumentsFromModel;
use MapleSyrupGroup\Search\Services\Importer\Documents\Validators\HydrationValidator as Validator;
use MapleSyrupGroup\Search\Services\Importer\Exceptions\ImportValidationException;
use MapleSyrupGroup\Search\Services\Importer\Exceptions\UnexpectedApiResponseException;
use MapleSyrupGroup\Search\Services\IndexBuilder\Index\Index;
use Psr\Log\LoggerInterface;

/**
 * For a specific index and domain id populate an elasticsearch index.
 */
class ElasticaDocumentHydrator implements DocumentHydrator
{
    /**
     * @var int
     */
    private $domainId;

    /**
     * @var string
     */
    private $typeName;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var Logger
     */
    private $businessEventLogger;

    /**
     * @var DocumentsFromModel
     */
    private $documentsFromModel;

    /**
     * @param int                $domainId
     * @param string             $typeName
     * @param Validator          $validator
     * @param Logger             $logger
     * @param DocumentsFromModel $model
     */
    public function __construct($domainId, $typeName, Validator $validator, Logger $logger, DocumentsFromModel $model)
    {
        $this->setDomainId($domainId);
        $this->setBusinessEventLogger($logger);
        $this->setTypeName($typeName);
        $this->setValidator($validator);
        $this->setDocumentsFromModel($model);
    }

    /**
     * Populate the index with documents for a specific Document ID.
     *
     * @param Index           $index
     * @param LoggerInterface $output
     *
     * @throws ImportValidationException
     * @throws UnexpectedApiResponseException
     */
    public function hydrate(Index $index, LoggerInterface $output)
    {
        $startTime = microtime(true);
        $type      = $index->getType($this->getTypeName());
        $total     = $this->getDocumentsFromModel()->insert($output, $type);

        $index->refresh();
        $this->getValidator()->validate($type, $this->getDomainId(), $total);
        $this->getBusinessEventLogger()->log(
            new Success($index->getName(), $type->getName(), $total, microtime(true) - $startTime)
        );
    }

    /**
     * @return int
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @param int $domainId
     */
    private function setDomainId($domainId)
    {
        $this->domainId = (int) $domainId;
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * @param string $typeName
     */
    private function setTypeName($typeName)
    {
        $this->typeName = (string) $typeName;
    }

    /**
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param Validator $validator
     */
    private function setValidator(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return Logger
     */
    public function getBusinessEventLogger()
    {
        return $this->businessEventLogger;
    }

    /**
     * @param Logger $businessEventLogger
     */
    private function setBusinessEventLogger(Logger $businessEventLogger)
    {
        $this->businessEventLogger = $businessEventLogger;
    }

    /**
     * @return DocumentsFromModel
     */
    public function getDocumentsFromModel()
    {
        return $this->documentsFromModel;
    }

    /**
     * @param DocumentsFromModel $documentsFromModel
     */
    public function setDocumentsFromModel(DocumentsFromModel $documentsFromModel)
    {
        $this->documentsFromModel = $documentsFromModel;
    }
}
