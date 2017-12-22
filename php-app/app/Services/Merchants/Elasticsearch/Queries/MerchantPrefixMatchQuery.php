<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchQuery;

/**
 * Find a merchant have the search term as prefix.
 */
class MerchantPrefixMatchQuery extends ElasticsearchQuery
{
    /**
     * @var array
     */
    protected $defaultSortOrder = [
        'clicks_value' => [
            'order' => 'desc'
        ]
    ];

    /**
     * Get an elastic search query to search by looking at the exact name of the merchant.
     *
     * @return array
     */
    public function generateQueryArray()
    {
        return [
            'query' => [
                'match' => [
                    'name_filtered.with_all_char_filtered_edge_ngram' => [
                        'query'    => $this->getSearchTerm(),
                        'analyzer' => 'keyword',
                    ],
                ],
            ],
            'sort'  =>  $this->prepareSortOrder(),
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
