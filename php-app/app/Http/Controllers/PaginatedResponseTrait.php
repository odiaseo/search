<?php

namespace MapleSyrupGroup\Search\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use MapleSyrupGroup\Search\Services\Merchants\Query;

/**
 * Return response in paginator.
 */
trait PaginatedResponseTrait
{
    /**
     * @param array $data
     * @param Query $query
     *
     * @return LengthAwarePaginator
     */
    public function paginateResponse(array $data, Query $query)
    {
        $options = [];
        $hits    = $data['hits']['hits'];
        $total   = $data['hits']['total'];

        foreach (array_keys($hits) as $key) {
            $hits[$key]['_source']['strategy']             = $data['strategy'];
            $hits[$key]['_source']['attempted_strategies'] = $data['attempted_strategies'];
        }

        $options['query'] = $this->getQueryParameters($query);

        return new LengthAwarePaginator($hits, $total, $query->getPageSize(), $query->getPage(), $options);
    }

    /**
     * @param Query $query
     *
     * @return array
     */
    protected function getQueryParameters(Query $query)
    {
        $page        = $query->getPage();
        $pageSize    = $query->getPageSize();
        $isInStore   = $query->getIsInStore();
        $queryParams = array_filter([
            'search_term'       => $query->getSearchTerm(),
            'language'          => $query->getLanguage(),
            'page'              => $page,
            'page_size'         => $pageSize,
            'sort_field'        => $query->getSortField(),
            'sort_order'        => $query->getSortDirection(),
            'exclude_merchants' => implode(',', $query->getExcludedMerchants()),
            'category_id'       => implode(',', $query->getCategoryIds()),
            'strategy'          => $query->getStrategy(),
        ]);

        if ($isInStore !== null) {
            $queryParams['is_in_store'] = $isInStore;
        }

        return $queryParams;
    }
}
