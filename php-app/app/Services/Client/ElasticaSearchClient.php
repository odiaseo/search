<?php

namespace MapleSyrupGroup\Search\Services\Client;

use Elastica\Client;
use MapleSyrupGroup\Search\Services\IndexBuilder\Index\SearchIndex;

/**
 * Class ElasticaSearchClient.
 */
class ElasticaSearchClient extends Client implements SearchClient
{
    /**
     * @param string $name
     *
     * @return SearchIndex
     */
    public function getIndex($name)
    {
        return new SearchIndex($this, $name);
    }
}
