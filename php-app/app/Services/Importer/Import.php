<?php

namespace MapleSyrupGroup\Search\Services\Importer;

use MapleSyrupGroup\Search\Services\Importer\Exceptions\IndexDefinitionException;
use MapleSyrupGroup\Search\Services\Importer\Exceptions\OutOfRetriesException;
use Psr\Log\LoggerInterface;

/**
 * Import from a different data sources to create a search index
 *
 * @package MapleSyrupGroup\Search\Services\Import
 */
interface Import
{
    /**
     * Import documents from different clients to make them searchable.
     *
     * @param string          $index
     * @param null|integer    $domainId
     * @param LoggerInterface $output
     * @param null|integer    $statusId
     *
     * @throws IndexDefinitionException
     * @throws OutOfRetriesException
     *
     * @return bool
     */
    public function doImport($index, $domainId, LoggerInterface $output, $statusId = null);
}
