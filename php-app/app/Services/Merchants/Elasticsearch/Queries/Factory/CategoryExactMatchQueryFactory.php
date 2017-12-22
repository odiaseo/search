<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory;

use MapleSyrupGroup\Search\Models\Merchants\Filters\CategoryNameFilter;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\CategoryExactMatchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\QueryFactory;
use MapleSyrupGroup\Search\Services\Merchants\Query;

class CategoryExactMatchQueryFactory implements QueryFactory
{
    /**
     * @var CategoryNameFilter
     */
    protected $filter;

    /**
     * CategoryExactMatchQueryFactory constructor.
     *
     * @param CategoryNameFilter|null $filter
     */
    public function __construct(CategoryNameFilter $filter = null)
    {
        $this->setFilter($filter);
    }

    /**
     * @param Query $query
     *
     * @return ElasticsearchQuery
     */
    public function create(Query $query)
    {
        return CategoryExactMatchQuery::fromQuery($query, $this->getFilter());
    }

    /**
     * @return CategoryNameFilter
     */
    protected function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param CategoryNameFilter $filter
     */
    protected function setFilter($filter = null)
    {
        if ($filter) {
            $this->filter = $filter;
        }
    }
}
