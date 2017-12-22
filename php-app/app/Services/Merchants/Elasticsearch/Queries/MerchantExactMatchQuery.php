<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchQuery;

/**
 * The query to try to pull back a merchant by getting an exact match on their name.
 */
class MerchantExactMatchQuery extends ElasticsearchQuery
{
    /**
     * @var array
     */
    protected $defaultSortOrder = [
        '_score' => [
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
                'dis_max' => [
                    'queries' => [
                        [
                            'match' => ['name.exact_match' => $this->getOriginalSearchTerm()],
                        ],
                        [
                            'match' => ['name_filtered.exact_match' => $this->getSearchTerm()],
                        ],
                    ],
                ],
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
