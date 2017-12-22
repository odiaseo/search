<?php

namespace MapleSyrupGroup\Search\Models\Merchants\Mapper;

use MapleSyrupGroup\Search\Models\SearchableModelMapper;

/**
 * Definition for the mapping for a merchant.
 *
 * This ensures regardless of the source, our merchants can use the same query
 */
class English implements SearchableModelMapper
{
    /**
     * Index mapping information.
     *
     * @var array
     */
    private $mappingProperties = [
        'name'                        => [
            'type'   => 'text',
            'fields' => [
                'exact_match' => [
                    'type'  => 'keyword',
                    'index' => true,
                ],
            ],
        ],
        'name_filtered'               => [
            'type'     => 'text',
            'analyzer' => 'custom_with_keyword',
            'fields'   => [
                'exact_match'                       => [
                    'type'  => 'keyword',
                    'index' => true,
                ],
                'with_all_chars_ngram'              => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_all_chars_ngram',
                ],
                'with_all_chars_edge_ngram'         => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_all_chars_edge_ngram',
                ],
                'with_letter_digit_ngram'           => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_letter_digit_ngram',
                ],
                'with_letter_digit_edge_ngram'      => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_letter_digit_edge_ngram',
                ],
                'with_all_char_filtered_edge_ngram' => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_all_chars_filtered_edge_ngram',
                ],
                'with_word_delimiter'               => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_word_delimiter',
                ],
            ],
        ],
        'description'                 => [
            'type'     => 'text',
            'analyzer' => 'english',
            'fields'   => [
                'with_snowball' => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_snowball',
                ],
            ],
        ],
        'keywords'                    => [
            'type'   => 'keyword',
            'fields' => [
                'with_letter_digit_edge_ngram' => [
                    'type'                   => 'text',
                    'position_increment_gap' => 500,
                    'analyzer'               => 'custom_with_letter_digit_edge_ngram',
                ],
                'with_stemmed_edge_ngram'      => [
                    'type'                   => 'text',
                    'position_increment_gap' => 500,
                    'analyzer'               => 'custom_with_all_chars_edge_ngram_stemmed',
                ],
                'with_all_chars'               => [
                    'type'     => 'text',
                    'analyzer' => 'standard',
                ],
            ],
        ],
        'categories'                  => [
            'type'       => 'nested',
            'properties' => [
                'id'       => [
                    'type' => 'integer',
                ],
                'weight'   => [
                    'type' => 'integer',
                ],
                'name'     => [
                    'type'   => 'text',
                    'fields' => [
                        'with_all_chars_filtered_edge_ngram' => [
                            'type'     => 'text',
                            'analyzer' => 'custom_with_all_chars_filtered_edge_ngram',
                        ],
                        'with_all_chars_edge_ngram'          => [
                            'type'     => 'text',
                            'analyzer' => 'custom_with_all_chars_edge_ngram',
                        ],
                        'exact_match'                        => [
                            'type' => 'keyword',
                        ],
                    ],
                ],
                'url_name' => [
                    'type' => 'keyword',
                ],
            ],
        ],
        'sales_value'                 => [
            'type' => 'double',
        ],
        'cashback_amount'             => [
            'type' => 'double',
        ],
        'cashback_percentage'         => [
            'type' => 'double',
        ],
        'clicks_value'                => [
            'type' => 'integer',
        ],
        'rates_text_filtered_active'  => [
            'copy_to'                => false,
            'type'                   => 'text',
            'position_increment_gap' => 100,
            'analyzer'               => 'standard',
            'store'                  => false,
            'fields'                 => [
                'with_stemmed_edge_ngram' => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_all_chars_edge_ngram_stemmed',
                ],
            ],
        ],
        'rates_text_filtered_expired' => [
            'copy_to'         => false,
            'type'                   => 'text',
            'position_increment_gap' => 100,
            'analyzer'               => 'standard',
            'store'                  => false,
            'fields'                 => [
                'with_stemmed_edge_ngram' => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_all_chars_edge_ngram_stemmed',
                ],
            ],
        ],
        'id'                          => [
            'type' => 'integer',
        ],
        'become_id'                   => [
            'type' => 'integer',
        ],
        'last_updated'                => [
            'type' => 'date',
        ],
        'is_in_store'                 => [
            'type' => 'integer',
        ],
        'related'                     => [
            'properties' => [
                'id'          => [
                    'type' => 'text',
                ],
                'is_in_store' => [
                    'type' => 'long',
                ],
                'name'        => [
                    'type' => 'text',
                ],
                'type'        => [
                    'type' => 'text',
                ],
                'url_name'    => [
                    'type' => 'text',
                ],
                'best_rates'  => [
                    'properties' => [
                        'rate_txt'     => [
                            'type' => 'text',
                        ],
                        'rate'         => [
                            'type' => 'text',
                        ],
                        'is_increased' => [
                            'type' => 'boolean',
                        ],
                        'is_exclusive' => [
                            'type' => 'boolean',
                        ],
                    ],
                ],
            ],
        ],
        'similar'                     => [
            'properties' => [
                'id'          => [
                    'type' => 'text',
                ],
                'is_in_store' => [
                    'type' => 'long',
                ],
                'name'        => [
                    'type' => 'text',
                ],
                'type'        => [
                    'type' => 'text',
                ],
                'url_name'    => [
                    'type' => 'text',
                ],
                'best_rates'  => [
                    'properties' => [
                        'rate_txt'     => [
                            'type' => 'text',
                        ],
                        'rate'         => [
                            'type' => 'text',
                        ],
                        'is_increased' => [
                            'type' => 'boolean',
                        ],
                        'is_exclusive' => [
                            'type' => 'boolean',
                        ],
                    ],
                ],
            ],
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function getMappingProperties()
    {
        return $this->mappingProperties;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguage()
    {
        return 'english';
    }
}
