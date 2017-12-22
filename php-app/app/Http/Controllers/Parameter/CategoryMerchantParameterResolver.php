<?php

namespace MapleSyrupGroup\Search\Http\Controllers\Parameter;

use MapleSyrupGroup\Search\Http\Request\ApiRequest;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;

/**
 * Resolve merchant search query parameters.
 */
class CategoryMerchantParameterResolver
{
    /**
     * @param ApiRequest $request
     * @param int $domainId
     *
     * @return Query
     */
    public function createQuery(ApiRequest $request, $domainId)
    {
        $this->validateSearchCriteria($request);
        $field     = $request->queryParam('sort_field');
        $direction = $request->queryParam('sort_order');
        $query     = new Query($request->queryParam('search_term'), $domainId, new SortParameter($field, $direction));

        $query->setLanguage($request->queryParam('language'));
        $query->setPage($request->queryParam('page'));
        $query->setPageSize($request->queryParam('page_size'));
        $query->setCategoryIds($request->queryParam('category_id'));
        $query->setDebug((bool)$request->queryParam('debug'));

        return $query;
    }

    /**
     * @param ApiRequest $request
     *
     * @return bool
     */
    private function validateSearchCriteria(ApiRequest $request)
    {
        if (empty($request->queryParam('search_term')) && empty($request->queryParam('category_id'))) {
            throw new InvalidCategorySearchCriteria('Either search_term or category_id must be provided');
        }

        return true;
    }
}
