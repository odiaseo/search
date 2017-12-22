<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Search;

class PrefixMatchSearch extends AbstractSearch
{
    /**
     * @return string
     */
    protected function getErrorMessage()
    {
        return 'Expected at least one hit for the prefix match search strategy.';
    }
}
