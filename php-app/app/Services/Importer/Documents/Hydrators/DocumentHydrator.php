<?php

namespace MapleSyrupGroup\Search\Services\Importer\Documents\Hydrators;

use MapleSyrupGroup\Search\Services\IndexBuilder\Index\Index;
use MapleSyrupGroup\Search\Services\Importer\Exceptions\ImportValidationException;
use MapleSyrupGroup\Search\Services\Importer\Exceptions\UnexpectedApiResponseException;
use Psr\Log\LoggerInterface;

/**
 * For a specific index and domain id populate an elasticsearch index
 *
 * @package MapleSyrupGroup\Search\Services\Import
 */
interface DocumentHydrator
{
    /**
     * Populate and index.
     *
     * Throws exception if for some reason the import fails.
     *
     * @param Index           $index
     * @param LoggerInterface $output
     *
     * @throws ImportValidationException
     * @throws UnexpectedApiResponseException
     */
    public function hydrate(Index $index, LoggerInterface $output);
}
