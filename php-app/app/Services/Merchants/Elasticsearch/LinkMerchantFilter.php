<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch;

use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Merchant;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\NoMerchantFoundException;

class LinkMerchantFilter
{
    /**
     * @var Merchant[]
     */
    private $merchants;

    /**
     * @param Merchant[] $merchants
     */
    public function __construct(array $merchants)
    {
        $this->merchants = $merchants;
    }

    /**
     * @param string $link
     *
     * @return Merchant
     *
     * @throws NoMerchantFoundException if none of the merchants matched the link
     */
    public function __invoke($link)
    {
        $merchants = $this->filterMerchants($link);

        if (isset($merchants[0])) {
            return $merchants[0];
        }

        throw new NoMerchantFoundException(sprintf('No merchant found for link: "%s".', $link));
    }

    /**
     * Elastic search returns results using the wildcard query which matches all links for the specified domain.
     * This filter ensures that only results that matches the url in the request is returned
     *
     * @param string $link
     *
     * @return Merchant[]
     */
    private function filterMerchants($link)
    {
        $linkMatchesOneOfMerchantLinks = function ($linkPattern) use ($link) {
            return preg_match('/https?:\/\/[^\/]*' . preg_quote($linkPattern, '/') . '/', $link);
        };

        $atLeastOneLinkMatchesMerchant = function (Merchant $merchant) use ($linkMatchesOneOfMerchantLinks) {
            return !empty(array_filter($merchant->getLinks(), $linkMatchesOneOfMerchantLinks));
        };

        return array_values(array_filter($this->merchants, $atLeastOneLinkMatchesMerchant));
    }
}
