<?php

namespace MapleSyrupGroup\Search\Models\Merchants\Mapper;

use MapleSyrupGroup\Search\Models\SearchableModelMapper;

/**
 * Definition for french specific mapping for merchant type.
 */
class French implements SearchableModelMapper
{
    /**
     * Index mapping properties.
     *
     * @var array
     */
    private $mappingProperties = [
        'id'                          => [
            'type' => 'integer',
        ],
        'become_id'                   => [
            'type' => 'integer',
        ],
        'domain_id'                   => [
            'type' => 'integer',
        ],
        'external_references'         => [
            'properties' => [
                'service' => [
                    'type' => 'text',
                ],
                'type'    => [
                    'type' => 'keyword',
                ],
                'ref'     => [
                    'type' => 'text',
                ],
            ],
        ],
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
            'analyzer' => 'french',
            'fields'   => [
                'exact_match'                         => [
                    'type'  => 'keyword',
                    'index' => true,
                ],
                'with_all_chars_ngram'                => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_all_chars_ngram',
                ],
                'with_letter_digit_edge_ngram_french' => [
                    'analyzer' => 'query_normaliser_french',
                    'type'     => 'text',
                ],
                'with_all_chars_edge_ngram'           => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_all_chars_edge_ngram',
                ],
                'with_letter_digit_ngram'             => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_letter_digit_ngram',
                ],
                'with_letter_digit_edge_ngram'        => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_letter_digit_edge_ngram',
                ],
                'with_all_char_filtered_edge_ngram'   => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_all_chars_filtered_edge_ngram',
                ],
                'with_word_delimiter'                 => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_word_delimiter',
                ],
            ],
        ],
        'status'                      => [
            'type' => 'keyword',
        ],
        'url_name'                    => [
            'type' => 'keyword',
        ],
        'description'                 => [
            'type'     => 'text',
            'analyzer' => 'french',
            'fields'   => [
                'with_snowball' => [
                    'type'     => 'text',
                    'analyzer' => 'custom_with_snowball',
                ],
            ],
        ],
        'live'                        => [
            'type' => 'boolean',
        ],
        'gambling'                    => [
            'type' => 'boolean',
        ],
        'adult'                       => [
            'type' => 'boolean',
        ],
        'toolbar_opt_out'             => [
            'type' => 'boolean',
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
        'categories'                  => [
            'type'       => 'nested',
            'properties' => [
                'id'       => [
                    'type'  => 'long',
                    'index' => true,
                ],
                'weight'   => [
                    'type' => 'long',
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
                            'type'  => 'keyword',
                            'index' => true,
                        ],
                    ],
                ],
                'url_name' => [
                    'type' => 'keyword',
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
        'images'                      => [
            'properties' => [
                'height'      => [
                    'type' => 'long',
                ],
                'width'       => [
                    'type' => 'long',
                ],
                'type_name'   => [
                    'type' => 'text',
                ],
                'path'        => [
                    'type' => 'keyword',
                ],
                'merchant_id' => [
                    'type' => 'long',
                ],
                'variant'     => [
                    'type' => 'long',
                ],
                'type_id'     => [
                    'type' => 'long',
                ],
            ],
        ],
        'best_rates'                  => [
            'properties' => [
                'rate'         => [
                    'type'   => 'text',
                    'fields' => [
                        'with_letter_digit_edge_ngram_english' => [
                            'analyzer' => 'custom_with_letter_digit_edge_ngram_english',
                            'type'     => 'text',
                        ],
                        'with_letter_digit_edge_ngram_french'  => [
                            'analyzer' => 'custom_with_letter_digit_edge_ngram_french',
                            'type'     => 'text',
                        ],
                    ],
                ],
                'is_exclusive' => [
                    'type' => 'boolean',
                ],
                'rate_text'    => [
                    'type'   => 'text',
                    'fields' => [
                        'with_letter_digit_edge_ngram_french'  => [
                            'analyzer' => 'custom_with_letter_digit_edge_ngram_french',
                            'type'     => 'text',
                        ],
                        'with_letter_digit_edge_ngram_english' => [
                            'analyzer' => 'custom_with_letter_digit_edge_ngram_english',
                            'type'     => 'text',
                        ],
                    ],
                ],
                'is_increased' => [
                    'type' => 'boolean',
                ],
            ],
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
        'similar'                     => [
            'properties' => [
                'merchant_id'  => [
                    'type' => 'long',
                ],
                'name'         => [
                    'type' => 'text',
                ],
                'id'           => [
                    'type' => 'text',
                ],
                'is_in_store'  => [
                    'type' => 'long',
                ],
                'url_name'     => [
                    'type' => 'text',
                ],
                'rate'         => [
                    'type' => 'text',
                ],
                'rate_text'    => [
                    'type' => 'text',
                ],
                'is_exclusive' => [
                    'type' => 'boolean',
                ],
                'is_inclusive' => [
                    'type' => 'boolean',
                ],
                'best_rates'   => [
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
        'related'                     => [
            'properties' => [
                'merchant_id'  => [
                    'type' => 'long',
                ],
                'name'         => [
                    'type' => 'text',
                ],
                'id'           => [
                    'type' => 'text',
                ],
                'is_in_store'  => [
                    'type' => 'long',
                ],
                'url_name'     => [
                    'type' => 'text',
                ],
                'rate'         => [
                    'type' => 'text',
                ],
                'rate_text'    => [
                    'type' => 'text',
                ],
                'is_exclusive' => [
                    'type' => 'boolean',
                ],
                'is_inclusive' => [
                    'type' => 'boolean',
                ],
                'best_rates'   => [
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
        'offers'                      => [
            'properties' => [
                'id'          => [
                    'type' => 'long',
                ],
                'title'       => [
                    'type' => 'text',
                ],
                'description' => [
                    'type' => 'text',
                ],
            ],
        ],
        'links'                       => [
            'type'  => 'keyword',
            'index' => true,
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
        return 'french';
    }
}
