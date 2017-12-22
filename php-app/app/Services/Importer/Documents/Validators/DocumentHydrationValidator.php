<?php

namespace MapleSyrupGroup\Search\Services\Importer\Documents\Validators;

use Elastica\Query;
use Elastica\Query\Term;
use Elastica\Search;
use Elastica\Type;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\DocumentInsert\ValidationFailed;
use MapleSyrupGroup\Search\Services\Importer\Exceptions\ImportValidationException;

/**
 * Validates that the number of documents that were inserted were the number we're expecting.
 */
class DocumentHydrationValidator implements HydrationValidator
{
    /**
     * @var BusinessEventLogger
     */
    private $businessEventLogger;

    /**
     * @var Query
     */
    private $query;

    /**
     * @var Term
     */
    private $term;

    /**
     * @var Search
     */
    private $verificationSearch;

    /**
     * DocumentHydrationValidator constructor.
     *
     * @param Search              $searcher
     * @param BusinessEventLogger $businessEventLogger
     * @param Query               $query
     * @param Term                $term
     */
    public function __construct(Search $searcher, BusinessEventLogger $businessEventLogger, Query $query, Term $term)
    {
        $this->setBusinessEventLogger($businessEventLogger);
        $this->setQuery($query);
        $this->setTerm($term);
        $this->setVerificationSearch($searcher);
    }

    /**
     * Validate the population of the records into the index.
     *
     * Throws an exception on the number of entries in the type doesn't match what's expected for the domain id
     *
     * @param Type $type
     * @param int  $domainId
     * @param int  $expected
     *
     * @throws ImportValidationException
     */
    public function validate(Type $type, $domainId, $expected)
    {
        $query = $this->getQuery();
        $index = $type->getIndex();
        $term  = $this->getTerm();

        $term->setTerm('domain_id', $domainId);
        $query->setQuery($term);

        $this->getVerificationSearch()->addIndex($index);

        $results = $this->getVerificationSearch()->search($query);
        $actual  = $results->getTotalHits();

        if ($actual !== $expected) {
            $this->getBusinessEventLogger()->log(
                new ValidationFailed($index->getName(), $type->getName(), $expected, $actual)
            );

            $message = "Was expecting $expected entries in index, instead I have $actual. Aborting";
            throw new ImportValidationException($message);
        }
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
    private function setBusinessEventLogger(BusinessEventLogger $businessEventLogger)
    {
        $this->businessEventLogger = $businessEventLogger;
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param Query $query
     */
    private function setQuery(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @return Term
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @param Term $term
     */
    private function setTerm(Term $term)
    {
        $this->term = $term;
    }

    /**
     * @return Search
     */
    public function getVerificationSearch()
    {
        return $this->verificationSearch;
    }

    /**
     * @param Search $verificationSearch
     */
    private function setVerificationSearch(Search $verificationSearch)
    {
        $this->verificationSearch = $verificationSearch;
    }
}
