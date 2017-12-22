<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Search;

class CategoryExactMatchSearch extends AbstractSearch
{
    /**
     * @return string
     */
    protected function getErrorMessage()
    {
        return 'Expected at least 1 hit for the category exact match strategy.';
    }
}
