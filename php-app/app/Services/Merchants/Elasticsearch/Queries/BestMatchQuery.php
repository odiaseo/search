<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchQuery;

/**
 * The query to try to pull back a merchant by getting an good match on an assortment of fields.
 */
class BestMatchQuery extends ElasticsearchQuery
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
     * Get an elastic search query to search by best match.
     *
     * @return array
     */
    public function generateQueryArray()
    {
        $searchTerm = $this->getSearchTerm();

        return [
            'query' => [
                'function_score' => [
                    'query'              => [
                        'bool' => [
                            'should' => [
                                [
                                    'nested' => [
                                        'path'  => 'categories',
                                        'query' => [
                                            'match' => [
                                                'categories.name.with_letter_digit_edge_ngram' => [
                                                    'query'    => $searchTerm,
                                                    'analyzer' => 'keyword',
                                                    'boost'    => 5,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                [
                                    'dis_max' => [
                                        'queries' => [
                                            [
                                                'constant_score' => [
                                                    'boost' => 50,
                                                    'filter' => [
                                                        'match' => [
                                                            'name_filtered.with_all_chars_edge_ngram' => [
                                                                'query'    => $searchTerm,
                                                                'analyzer' => 'keyword',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                            [
                                                'bool' => [
                                                    'should' => [
                                                        [
                                                            'constant_score' => [
                                                                'boost' => 8,
                                                                'filter' => [
                                                                    'match' => [
                                                                        'keywords.with_all_chars' => [
                                                                            'query'    => $searchTerm,
                                                                            'operator' => 'and',
                                                                        ],
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                        [
                                                            'constant_score' => [
                                                                'boost' => 10,
                                                                'filter' => [
                                                                    'match' => [
                                                                        'rates_text_filtered_active.with_stemmed_edge_ngram' => [
                                                                            'query'    => $searchTerm,
                                                                            'operator' => 'and',
                                                                        ],
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                        [
                                                            'constant_score' => [
                                                                'boost' => 5,
                                                                'filter' => [
                                                                    'match' => [
                                                                        'rates_text_filtered_expired.with_stemmed_edge_ngram' => [
                                                                            'query'    => $searchTerm,
                                                                            'operator' => 'and',
                                                                        ],
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                        [
                                                            'match' => [
                                                                'name_filtered.with_all_chars_edge_ngram' => [
                                                                    'query'         => $searchTerm,
                                                                    'analyzer'      => 'keyword',
                                                                    'boost'         => '0.1',
                                                                    'fuzziness'     => 1,
                                                                    'prefix_length' => 1,
                                                                ],
                                                            ],
                                                        ],
                                                        [
                                                            'match' => [
                                                                'name_filtered.with_all_chars_ngram' => [
                                                                    'query'    => $searchTerm,
                                                                    'analyzer' => 'standard',
                                                                    'boost'    => 3,
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'field_value_factor' => [
                        'field'    => 'clicks_value',
                        'modifier' => 'log1p',
                        'factor'   => '1',
                    ],
                    'max_boost'          => '3.6',
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
