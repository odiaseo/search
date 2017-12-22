<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Stubs;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchQuery;

class ElasticsearchQueryStub extends ElasticsearchQuery
{
    const TYPE = 'messages';

    /**
     * @return array
     */
    protected function generateQueryArray()
    {
        return [
            'query' => [
                'function_score' => [
                    'query' => [
                        'match' => [
                            'message' => $this->searchTerm,
                        ]
                    ]
                ]
            ],
            'sort'  => [
                '_score' => [
                    'order' => 'desc'
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }
}
