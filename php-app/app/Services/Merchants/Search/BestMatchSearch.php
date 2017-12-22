<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Search;

class BestMatchSearch extends AbstractSearch
{
    /**
     * @return string
     */
    protected function getErrorMessage()
    {
        return 'Expected at least one hit for the best match search strategy.';
    }
}
