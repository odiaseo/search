<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchQuery;

/**
 * The query to try to pull back merchant by searching merchant that have the exact phrase contained
 * in the category.name.exact_match field ordered by weighted categories scores in descending order.
 */
class CategoryExactMatchQuery extends ElasticsearchQuery
{
    /**
     * Get an elastic search query to search by looking at the categories.exact_match field.
     *
     * @return array
     */
    public function generateQueryArray()
    {
        return [
            'query' => [
                'nested' => [
                    'path'  => 'categories',
                    'query' => $this->getQueryParams(),
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
            return $this->getSortOrder('categories.name.exact_match');
        }

        return $this->sortOrder;
    }

    /**
     * Sort results by weighted categories.
     *
     * @param string $field
     *
     * @return array
     */
    protected function getSortOrder($field)
    {
        return [
            'categories.weight' => [
                'mode'          => 'max',
                'order'         => 'desc',
                'missing'       => PHP_INT_MAX - 1,
                'nested_path'   => 'categories',
                'nested_filter' => $this->getSortFilterParams($field),
            ],
        ];
    }

    /**
     * @param $field
     *
     * @return array
     */
    private function getSortFilterParams($field)
    {
        if (empty($this->getOriginalSearchTerm()) && !empty($this->categoryIds)) {
            return $this->getCategoryIdQuery();
        }

        return [
            'term' => [
                $field => $this->getSearchTerm(),
            ],
        ];
    }

    /**
     * @return array
     */
    private function getQueryParams()
    {
        if (empty($this->getOriginalSearchTerm()) && !empty($this->categoryIds)) {
            return $this->getCategoryIdQuery();
        }

        return ['bool' => $this->getSearchQueryWithFilter()];
    }

    /**
     * @return array
     */
    private function getSearchQueryWithFilter()
    {
        $boolQuery = [
            'must' => [
                [
                    'match' => [
                        'categories.name.exact_match' => $this->getSearchTerm(),
                    ],
                ],
            ],
        ];

        if (!empty($this->categoryIds)) {
            $boolQuery['filter'] = $this->getCategoryIdQuery();
        }

        return $boolQuery;
    }

    /**
     * @return array
     */
    private function getCategoryIdQuery()
    {
        return [
            'terms' => [
                'categories.id' => (array) $this->categoryIds,
            ],
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
