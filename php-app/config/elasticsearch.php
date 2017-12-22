<?php

use MapleSyrupGroup\QCommon\Enum\DomainEnum;
use MapleSyrupGroup\Search\Logs\ConfigureLogging;
use MapleSyrupGroup\Search\Models\Merchants\DataSource\ContentApi;
use MapleSyrupGroup\Search\Models\Merchants\DataSource\QuidcoApi;
use MapleSyrupGroup\Search\Models\Merchants\Mapper\English;
use MapleSyrupGroup\Search\Models\Merchants\Mapper\French;

return [
    'search_strategy_log_enabled' => (bool) env('SEARCH_STRATEGY_LOG_ENABLED', false),
    'enabled'                     => (bool) env('ELASTICSEARCH_ENABLED', true),
    'benchmarking'                => [
        'file_path'        => storage_path('benchmarking/elasticsearch'),
        'input_csv'        => 'top_2500_merchant_terms.csv', // CSV file that contains the top 2500 search terms
        'input_csv_offset' => 0,
    ],
    'cluster_nodes'               => env('ELASTICSEARCH_CLUSTER_NODES', ''),
    'client'                      => [
        'connectionStrategy' => env('ELASTICSEARCH_STRATEGY', 'RoundRobin'),
        'servers'            => [
            [
                'host'             => env('ELASTICSEARCH_HOST'),
                'port'             => env('ELASTICSEARCH_PORT'),
                'bigintConversion' => true,
            ],
        ],
        'bulk_max_size'      => env('ELASTICSEARCH_BULK_SIZE_MAX', 100),
        'max_execution_time' => env('ELASTICSEARCH_BUILD_INDEX_EXECUTION_TIME', 600),
    ],

    'indexes' => [
        ConfigureLogging::ELASTICSEARCH_INDEX => [
            'settings' => [
                'number_of_shards'   => (int) env('ELASTICSEARCH_SHARDS', 5),
                'number_of_replicas' => (int) env('ELASTICSEARCH_REPLICAS', 2),
                'index.store.type'   => 'niofs',
            ],
            'types'    => [
                ConfigureLogging::ELASTICSEARCH_TYPE => [
                    'level'    => [
                        'type' => 'integer',
                    ],
                    'message'  => [
                        'type'  => 'keyword',
                        'index' => true,
                    ],
                    'datetime' => [
                        'type' => 'date',
                    ],
                    'channel'  => [
                        'type'  => 'keyword',
                        'index' => true,
                    ],
                    'context'  => [
                        'type'       => 'object',
                        'properties' => [
                            'class'  => [
                                'type'  => 'keyword',
                                'index' => true,
                            ],
                            'entity' => [
                                'type'    => 'object',
                                'enabled' => false,
                            ],
                        ],
                    ],
                    'extra'    => [
                        'type'       => 'object',
                        'properties' => [
                            'url'         => [
                                'type'  => 'keyword',
                                'index' => true,
                            ],
                            'referrer'    => [
                                'type'  => 'keyword',
                                'index' => true,
                            ],
                            'http_method' => [
                                'type'  => 'keyword',
                                'index' => true,
                            ],
                            'server'      => [
                                'type'  => 'keyword',
                                'index' => true,
                            ],
                            'ip'          => [
                                'type' => 'ip',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        env('ELASTICSEARCH_INDEX_NAME')       => [
            'settings'      => [
                'number_of_shards'   => (int) env('ELASTICSEARCH_SHARDS', 5),
                'number_of_replicas' => (int) env('ELASTICSEARCH_REPLICAS', 3),
                'index.store.type'   => env('ELASTICSEARCH_STORAGE', 'niofs'),
                'analysis'           => [
                    'analyzer'    => [
                        // Used for normalising the data users send to us, so we can match it against our indexes
                        // without worrying about "&" vs " and " (or et in french), or diacritics (e.g. é becomes e)
                        // hyphens are also normalised to spaces, and apostrophes are ignored.
                        'query_normaliser_french'                     => [
                            'tokenizer'   => 'keyword',
                            'filter'      => [
                                'lowercase',
                                'custom_ascii_folding',
                            ],
                            'char_filter' => [
                                'custom_apostrophe_strip',
                                'custom_hyphen_mapping',
                                'custom_dots_spaces_mapping',
                                'custom_ampersand_mapping_french',
                            ],
                        ],

                        // Can we match the letters from the edge of the merchant name (higher value)
                        // E.g. "My Hotel" => "My", "Ho", "Hot", "Hote", "Hotel"
                        'custom_with_letter_digit_edge_ngram_english' => [
                            'tokenizer'   => 'custom_all_chars_edge_ngram',
                            'filter'      => [
                                'lowercase',
                                'custom_ascii_folding',
                                'custom_limit',
                            ],
                            'char_filter' => [
                                'custom_apostrophe_strip',
                                'custom_hyphen_mapping',
                                'custom_dots_spaces_mapping',
                                'custom_ampersand_mapping_english',
                            ],
                        ],
                        'custom_with_letter_digit_edge_ngram_french'  => [
                            'tokenizer'   => 'custom_all_chars_edge_ngram',
                            'filter'      => [
                                'lowercase',
                                'custom_ascii_folding',
                                'custom_limit',
                            ],
                            'char_filter' => [
                                'custom_apostrophe_strip',
                                'custom_hyphen_mapping',
                                'custom_dots_spaces_mapping',
                                'custom_ampersand_mapping_french',
                            ],
                        ],

                        'custom_with_all_chars_edge_ngram'          => [
                            'tokenizer' => 'custom_all_chars_edge_ngram',
                            'filter'    => ['lowercase', 'custom_ascii_folding', 'custom_limit'],
                        ],
                        'custom_with_all_chars_ngram'               => [
                            'tokenizer' => 'custom_all_chars_ngram',
                            'filter'    => ['lowercase', 'custom_ascii_folding', 'custom_limit'],
                        ],
                        'custom_with_letter_digit_edge_ngram'       => [
                            'tokenizer'   => 'custom_letter_digit_edge_ngram',
                            'filter'      => ['lowercase', 'custom_ascii_folding', 'custom_limit'],
                            'char_filter' => ['custom_hyphen_mapping', 'custom_ampersand_mapping'],
                        ],
                        'custom_with_all_chars_filtered_edge_ngram' => [
                            'tokenizer' => 'custom_all_chars_edge_ngram',
                            'filter'    => ['lowercase', 'custom_ascii_folding', 'custom_limit'],
                        ],
                        'custom_with_all_chars_edge_ngram_stemmed'  => [
                            'tokenizer'   => 'custom_all_chars_edge_ngram',
                            'filter'      => [
                                'lowercase',
                                'custom_english_possessive_stemmer',
                                'custom_english_stemmer',
                                'custom_limit',
                            ],
                            'char_filter' => ['custom_hyphen_mapping', 'custom_ampersand_mapping'],
                        ],
                        'custom_with_letter_digit_ngram'            => [
                            'tokenizer'   => 'custom_all_chars_ngram',
                            'filter'      => ['lowercase', 'custom_limit'],
                            'char_filter' => ['custom_hyphen_mapping', 'custom_ampersand_mapping'],
                        ],
                        'custom_with_snowball'                      => [
                            'tokenizer'   => 'standard',
                            'filter'      => ['lowercase', 'custom_snowball'],
                            'char_filter' => ['custom_char_mapping'],
                        ],
                        'custom_with_keyword'                       => [
                            'tokenizer' => 'keyword',
                            'filter'    => ['lowercase'],
                        ],
                        'custom_with_ampersand_mapping'             => [
                            'tokenizer'   => 'keyword',
                            'filter'      => ['lowercase'],
                            'char_filter' => 'custom_ampersand_mapping',
                        ],
                        'custom_with_word_delimiter'                => [
                            'tokenizer'   => 'whitespace',
                            'filter'      => ['lowercase', 'custom_word_delimiter'],
                            'char_filter' => ['custom_ampersand_mapping'],
                        ],
                    ],
                    'tokenizer'   => [
                        'custom_all_chars_edge_ngram'    => [
                            'type'        => 'edge_ngram',
                            'min_gram'    => 1,
                            'max_gram'    => 40,
                            'token_chars' => ['letter', 'digit', 'whitespace', 'punctuation'],
                        ],
                        'custom_all_chars_ngram'         => [
                            'type'        => 'ngram',
                            'min_gram'    => 1,
                            'max_gram'    => 40,
                            'token_chars' => ['letter', 'digit'],
                        ],
                        'custom_letter_digit_edge_ngram' => [
                            'type'        => 'edge_ngram',
                            'min_gram'    => 1,
                            'max_gram'    => 40,
                            'token_chars' => ['letter', 'digit'],
                        ],

                        'custom_letter_digit_ngram' => [
                            'type'        => 'ngram',
                            'min_gram'    => 1,
                            'max_gram'    => 20,
                            'token_chars' => ['letter', 'digit'],
                        ],
                    ],
                    'filter'      => [
                        'custom_word_delimiter' => [
                            'type'                    => 'word_delimiter',
                            'stem_english_possessive' => false,
                            'catenate_words'          => true,
                            'catenate_numbers'        => true,
                            'catenate_all'            => true,
                            'split_on_case_change'    => true,
                            'preserve_original'       => true,
                            'split_on_numerics'       => true,
                        ],
                        'stopwords_english'     => [
                            'type'        => 'stop',
                            'ignore_case' => true,
                            'stopwords'   => '_english_',
                        ],
                        'stopwords_french'      => [
                            'type'        => 'stop',
                            'ignore_case' => true,
                            'stopwords'   => '_french_',
                        ],
                        'custom_limit'          => [
                            'type'            => 'limit',
                            'max_token_count' => 1000,
                        ],
                        'custom_ascii_folding'  => [
                            'type'              => 'asciifolding',
                            'preserve_original' => true,
                        ],
                        'concatenation'         => [
                            'type'             => 'shingle',
                            'max_shingle_size' => 5,
                            'output_unigrams'  => true,
                            'token_separator'  => '',
                        ],

                        'custom_snowball'                   => [
                            'type'     => 'snowball',
                            'language' => 'English',
                        ],
                        'custom_english_stemmer'            => [
                            'type'     => 'stemmer',
                            'language' => 'porter2',
                        ],
                        'custom_english_possessive_stemmer' => [
                            'type'     => 'stemmer',
                            'language' => 'possessive_english',
                        ],
                    ],
                    'char_filter' => [
                        'custom_char_mapping'              => [
                            'type'     => 'mapping',
                            'mappings' => [
                                ' > => a',
                                ' 0 => o',
                            ],
                        ],
                        'custom_hyphen_mapping'            => [
                            'type'        => 'pattern_replace',
                            'pattern'     => '-+',
                            'replacement' => ' ',
                        ],
                        'custom_dots_spaces_mapping'       => [
                            'type'        => 'pattern_replace',
                            'pattern'     => '\\.+',
                            'replacement' => ' ',
                        ],
                        'custom_ampersand_mapping_english' => [
                            'type'        => 'pattern_replace',
                            'pattern'     => '&| & ',
                            'replacement' => ' and ',
                        ],
                        'custom_ampersand_mapping_french'  => [
                            'type'        => 'pattern_replace',
                            'pattern'     => '&| & ',
                            'replacement' => ' et ',
                        ],
                        'custom_apostrophe_strip'          => [
                            'type'        => 'pattern_replace',
                            'pattern'     => "'+",
                            'replacement' => '',
                        ],
                        'custom_whitespace_strip'          => [
                            'type'        => 'pattern_replace',
                            'pattern'     => '\\s+',
                            'replacement' => '',
                        ],
                        'custom_ampersand_mapping'         => [
                            'type'        => 'pattern_replace',
                            'pattern'     => '&| & ',
                            'replacement' => ' and ',
                        ],
                    ],
                ],
            ],
            'class_types'   => [
                'merchants' => [
                    DomainEnum::DOMAIN_ID_QUIDCO => QuidcoApi::class,
                    DomainEnum::DOMAIN_ID_SHOOP  => ContentApi::class,
                ],
            ],
            'mapping_types' => [
                'merchants' => [
                    DomainEnum::DOMAIN_ID_QUIDCO => English::class,
                    DomainEnum::DOMAIN_ID_SHOOP  => French::class,
                ],
            ],
        ],
    ],

    'stopWords'    => [
        'english' => [
            'rates_text' => include __DIR__ . '/../resources/stopWords/english/rates_text.php',
        ],
    ],
    'replacements' => [
        'merchant_name' => [
            'english' => [
                ' & ' => ' and ',
                '& '  => ' and ',
                ' &'  => ' and ',
                '&'   => ' and ',
                ' - ' => '',
                '-'   => '',
                '.'   => '',
                '('   => '',
                ')'   => '',
                '®'   => '',
                '†'   => '',
            ],
            'french'  => [
                ' & ' => ' et ',
                '& '  => ' et ',
                ' &'  => ' et ',
                '&'   => ' et ',
                ' - ' => '',
                '-'   => '',
                '.'   => '',
                '('   => '',
                ')'   => '',
                '®'   => '',
                '†'   => '',
            ],
        ],
        'category_name' => [
            'english' => [
                ' & ' => ' and ',
                '& '  => ' and ',
                ' &'  => ' and ',
                '&'   => ' and ',
                '-'   => ' ',
            ],
            'french'  => [
                ' & ' => ' et ',
                '& '  => ' et ',
                ' &'  => ' et ',
                '&'   => ' et ',
                '-'   => ' ',
            ],
        ],
    ],
];
