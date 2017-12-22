<?php

namespace MapleSyrupGroup\Search\Services\Merchants\FindByUrl;

/**
 * The Merchants repository.
 */
interface Merchants
{
    /**
     * @param LinkQuery $query
     *
     * @return Merchant
     *
     * @throws NoMerchantFoundException if no merchant was found matching the query criteria
     */
    public function getByLink(LinkQuery $query);
}
