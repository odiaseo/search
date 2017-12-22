<?php

namespace MapleSyrupGroup\Search\Services\Importer\Documents\Validators;

use Elastica\Type;
use MapleSyrupGroup\Search\Services\Importer\Exceptions\ImportValidationException;

/**
 * Checks that documents that were inserted are found when we query them
 *
 * @package MapleSyrupGroup\Search\Services\Importer\Documents
 */
interface HydrationValidator
{
    /**
     * Validate the population of the records into the index.
     *
     * Throws an exception on the number of entries in the type does not match what's expected for the domain id
     *
     * @param Type    $type
     * @param integer $domainId
     * @param integer $expected
     *
     * @throws ImportValidationException
     */
    public function validate(Type $type, $domainId, $expected);
}
