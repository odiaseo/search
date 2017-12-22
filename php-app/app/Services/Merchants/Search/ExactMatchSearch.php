<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Search;

class ExactMatchSearch extends AbstractSearch
{
    /**
     * @return string
     */
    protected function getErrorMessage()
    {
        return 'Expected to get exactly one hit but got %d.';
    }

    /**
     * @param array $resultSet
     *
     * @return bool
     */
    public function isValid(array $resultSet)
    {
        return $resultSet['hits']['total'] === 1;
    }
}
