<?php

namespace MapleSyrupGroup\Search\Services\Importer\Types;

use MapleSyrupGroup\Search\Services\IndexBuilder\Index\Index;
use Psr\Log\LoggerInterface;

/**
 * Build a type based on some data
 *
 * @package MapleSyrupGroup\Search\Services\Import
 */
interface TypeBuilder
{
    /**
     * Build the type in the index
     *
     * @param Index           $index
     * @param LoggerInterface $output
     *
     * @return
     */
    public function build(Index $index, LoggerInterface $output);
}
