<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Factory;

use MapleSyrupGroup\Search\Services\Merchants\Search;
use MapleSyrupGroup\Search\Services\Merchants\Search\Timer;

/**
 * Setup category Merchant search factory with category_exact_match.
 */
class CategoryMerchantSearchFactory extends SearchFactory
{
    /**
     * @return Search
     */
    public function createSearch()
    {
        return $this->getCategoryExactMatchStrategy(new Timer());
    }
}
