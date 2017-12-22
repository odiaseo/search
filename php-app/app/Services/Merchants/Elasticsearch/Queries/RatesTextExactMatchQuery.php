<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchQuery;

/**
 * The query to try to pull back a merchant by searching merchant that have the exact phrase contained
 * in the rates_text_filtered field.
 */
class RatesTextExactMatchQuery extends ElasticsearchQuery
{
    /**
     * @var array
     */
    protected $defaultSortOrder = [
        '_score'                    => [
            'order' => 'desc',
        ],
        'name_filtered.exact_match' => [
            'order' => 'asc',
        ],
    ];

    /**
     * Get an elastic search query to search by looking at the rates_text_filtered field.
     *
     * @return array
     */
    public function generateQueryArray()
    {
        return [
            'query' => [
                'match_phrase' => ['rates_text_filtered' => $this->getSearchTerm()],
            ],
            'sort'  => $this->prepareSortOrder(),
        ];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'merchants';
    }
}
