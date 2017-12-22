<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory;

use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantNameFilter;

trait MerchantQueryFilterTrait
{
    /**
     * @var MerchantNameFilter
     */
    protected $filter;

    /**
     * MerchantQueryFilterTrait constructor.
     *
     * @param MerchantNameFilter|null $filter
     */
    public function __construct(MerchantNameFilter $filter = null)
    {
        $this->setFilter($filter);
    }

    /**
     * @return MerchantNameFilter
     */
    protected function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param MerchantNameFilter $filter
     */
    protected function setFilter($filter = null)
    {
        if ($filter) {
            $this->filter = $filter;
        }
    }
}
