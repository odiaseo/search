<?php

namespace MapleSyrupGroup\Search\Models\Merchants\Filters;

class MerchantFilterAggregate
{
    /**
     * @var StopWordFilter
     */
    private $stopWordFilter;

    /**
     * @var CategoryNameFilter
     */
    private $categoryFilter;

    /**
     * @var MerchantNameFilter
     */
    private $merchantNameFilter;

    /**
     * MerchantFilterAggregate constructor.
     *
     * @param StopWordFilter     $stopWordFilter
     * @param CategoryNameFilter $categoryFilter
     * @param MerchantNameFilter $merchantFilter
     */
    public function __construct(
        StopWordFilter $stopWordFilter,
        CategoryNameFilter $categoryFilter,
        MerchantNameFilter $merchantFilter
    ) {
        $this->setStopWordFilter($stopWordFilter);
        $this->setCategoryFilter($categoryFilter);
        $this->setMerchantNameFilter($merchantFilter);
    }

    /**
     * @return MerchantNameFilter
     */
    public function getMerchantNameFilter()
    {
        return $this->merchantNameFilter;
    }

    /**
     * @param MerchantNameFilter $merchantNameFilter
     *
     * @return $this
     */
    public function setMerchantNameFilter($merchantNameFilter)
    {
        $this->merchantNameFilter = $merchantNameFilter;

        return $this;
    }

    /**
     * @return StopWordFilter
     */
    public function getStopWordFilter()
    {
        return $this->stopWordFilter;
    }

    /**
     * @param StopWordFilter $stopWordFilter
     *
     * @return $this
     */
    private function setStopWordFilter($stopWordFilter)
    {
        $this->stopWordFilter = $stopWordFilter;

        return $this;
    }

    /**
     * @return CategoryNameFilter
     */
    public function getCategoryFilter()
    {
        return $this->categoryFilter;
    }

    /**
     * @param CategoryNameFilter $categoryFilter
     *
     * @return $this
     */
    private function setCategoryFilter($categoryFilter)
    {
        $this->categoryFilter = $categoryFilter;

        return $this;
    }
}
