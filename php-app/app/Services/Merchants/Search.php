<?php

namespace MapleSyrupGroup\Search\Services\Merchants;

use MapleSyrupGroup\Search\Exceptions\SearchException;

interface Search
{
    /**
     * @param Query $query
     *
     * @throws SearchException if search fails for any reason
     *
     * @return array
     */
    public function search(Query $query);
}
