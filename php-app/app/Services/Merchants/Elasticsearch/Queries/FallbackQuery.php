<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchQuery;

/**
 * A query that will probably pull back something even if it's not exactly what we're looking for.
 */
class FallbackQuery extends ElasticsearchQuery
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
     * Get an elastic search query to search in a way that will return pretty much any merchant but not very accurately.
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
                                    'constant_score' => [
                                        'boost' => '1',
                                        'filter' => [
                                            'match' => [
                                                'keywords.with_letter_digit_edge_ngram' => [
                                                    'query'     => $searchTerm,
                                                    'analyzer'  => 'keyword',
                                                    'fuzziness' => 2,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                [
                                    'constant_score' => [
                                        'boost' => '3',
                                        'filter' => [
                                            'match' => [
                                                'rates_text_filtered_active.with_stemmed_edge_ngram' => [
                                                    'query'     => $searchTerm,
                                                    'operator'  => 'and',
                                                    'fuzziness' => 2,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                [
                                    'constant_score' => [
                                        'boost' => '2',
                                        'filter' => [
                                            'match' => [
                                                'rates_text_filtered_expired.with_stemmed_edge_ngram' => [
                                                    'query'     => $searchTerm,
                                                    'operator'  => 'and',
                                                    'fuzziness' => 2,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                [

                                    'multi_match' => [
                                        'query'     => $searchTerm,
                                        'type'      => 'most_fields',
                                        'fuzziness' => 3,
                                        'fields'    => [
                                            'name_filtered.with_letter_digit_ngram',
                                        ],
                                    ],

                                ],
                            ],
                        ],
                    ],
                    'field_value_factor' => [
                        'field'    => 'clicks_value',
                        'modifier' => 'log1p',
                        'factor'   => '1.0',
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
