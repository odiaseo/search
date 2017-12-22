<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries;

/**
 * The query to try to pull back merchant by searching category names that start with the search term
 * the results is ordered by popularity.
 */
class CategoryPrefixMatchQuery extends CategoryExactMatchQuery
{
    /**
     * {@inheritdoc}
     */
    public function generateQueryArray()
    {
        return [
            'query' => [
                'nested' => [
                    'path'  => 'categories',
                    'query' => [
                        'match' => [
                            'categories.name.with_all_chars_filtered_edge_ngram' => [
                                'query'    => $this->getSearchTerm(),
                                'analyzer' => 'keyword',
                            ],
                        ],
                    ],
                ],
            ],
            'sort'  => $this->prepareSortOrder(),
        ];
    }

    /**
     * @return array
     */
    public function prepareSortOrder()
    {
        if (empty($this->sortOrder)) {
            return $this->getSortOrder('categories.name.with_all_chars_filtered_edge_ngram');
        }

        return $this->sortOrder;
    }
}
