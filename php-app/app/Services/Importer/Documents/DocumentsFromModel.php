<?php

namespace MapleSyrupGroup\Search\Services\Importer\Documents;

use Elastica\Type;
use Psr\Log\LoggerInterface;

/**
 * Inserts documents from a model into the Index
 *
 * @package MapleSyrupGroup\Search\Services\Importer\Documents
 */
interface DocumentsFromModel
{
    /**
     * Insert the documents from this model into the index
     *
     * Returns the number of documents that should have been inserted
     *
     * @param LoggerInterface $output
     * @param Type            $type
     *
     * @return integer
     */
    public function insert(LoggerInterface $output, Type $type);
}
