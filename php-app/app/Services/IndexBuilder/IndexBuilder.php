<?php

namespace MapleSyrupGroup\Search\Services\IndexBuilder;

use MapleSyrupGroup\Search\Services\Importer\Exceptions\IndexBuildException;
use Psr\Log\LoggerInterface;

/**
 * Build an index and all the types associated with it.
 *
 * @package MapleSyrupGroup\Search\Services\Import
 */
interface IndexBuilder
{
    /**
     * Build an index
     *
     * @param string          $indexName
     * @param LoggerInterface $output
     *
     * @throws IndexBuildException
     */
    public function build($indexName, LoggerInterface $output);
}
