<?php

namespace MapleSyrupGroup\Search\Services\Merchants;

trait QueryFilterTrait
{
    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->filters['status'] = $status;

        return $this;
    }

    /**
     * Filter whether the merchant is available in store.
     *
     * @param null|int $isInStore
     *
     * @return $this
     */
    public function setIsInStore($isInStore)
    {
        if ($isInStore === 0) {
            $this->filters[self::FIELD_IS_IN_STORE] = 0;
        } elseif ($isInStore === 1) {
            $this->filters[self::FIELD_IS_IN_STORE] = 1;
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getIsInStore()
    {
        if (array_key_exists(self::FIELD_IS_IN_STORE, $this->filters)) {
            return $this->filters[self::FIELD_IS_IN_STORE];
        }

        return null;
    }

    /**
     * @param bool $showMerchants
     *
     * @return $this
     */
    public function setShowAdultMerchants($showMerchants)
    {
        $this->filters['adult'] = $showMerchants;

        return $this;
    }

    /**
     * @param bool $showMerchants
     *
     * @return $this
     */
    public function setShowGamblingMerchants($showMerchants)
    {
        $this->filters['gambling'] = $showMerchants;

        return $this;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }
}
