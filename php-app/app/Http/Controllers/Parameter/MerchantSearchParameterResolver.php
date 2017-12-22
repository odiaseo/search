<?php

namespace MapleSyrupGroup\Search\Http\Controllers\Parameter;

use MapleSyrupGroup\Search\Http\Request\ApiRequest;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;

/**
 * Resolved query parameters from the request.
 */
class MerchantSearchParameterResolver
{
    /**
     * @param ApiRequest $request
     * @param int        $domainId
     *
     * @return Query
     */
    public function createQuery(ApiRequest $request, $domainId)
    {
        $field     = $request->queryParam('sort_field');
        $direction = $request->queryParam('sort_order');
        $query     = new Query($request->queryParam('search_term'), $domainId, new SortParameter($field, $direction));

        if ($request->queryParam('status')) {
            $query->setStatus($request->queryParam('status'));
        }

        $query->setLanguage($request->queryParam('language'));
        $query->setPage($request->queryParam('page'));
        $query->setPageSize($request->queryParam('page_size'));
        $query->setExcludedMerchants($request->queryParam('exclude_merchants'));
        $query->setDebug((bool) $request->queryParam('debug'));
        $query->setStrategy($request->queryParam('strategy'));

        return $this->setInStoreFilter($query, $request);
    }

    /**
     * @param Query      $query
     * @param ApiRequest $request
     *
     * @return Query
     */
    public function setInStoreFilter(Query $query, ApiRequest $request)
    {
        $inStoreFlag = $this->getBooleanValue($request->query('in_store', ''));

        if ($inStoreFlag === false) {
            $query->setIsInStore(0);
        } elseif ($inStoreFlag === true) {
            $query->setIsInStore(1);
        }

        return $query;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function getBooleanValue($value)
    {
        if (null !== $value && '' !== $value) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return $value;
    }
}
