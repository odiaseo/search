<?php

namespace MapleSyrupGroup\Search\Behat\Context;

use Behat\Behat\Context\Context;
use MapleSyrupGroup\Search\Behat\Search\MerchantRepository;
use PHPUnit_Framework_Assert as PHPUnit;

class ExcludeMerchantContext implements Context
{
    const DOMAIND_ID = 1;

    /**
     * @var MerchantRepository
     */
    private $merchantRepository;

    /**
     * @var array
     */
    private $lastResponse;

    /**
     * Map merchant names to IDs.
     *
     * @var array
     */
    private $merchantMapping = [
        244   => 'Hotels.com',
        169   => 'Asos',
        2490  => 'ao.com',
        8945  => 'Booking Buddy',
        11937 => 'Tesco Wine By The Case',
        7289  => 'Asda',
        341   => 'PetPlanet',
        5355  => 'George',
        7907  => 'Direct Line Car Insurance',
    ];

    /**
     * @param MerchantRepository $merchantRepository
     */
    public function __construct(MerchantRepository $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * @Then the filtered :merchants should not be returned
     */
    public function theFilteredMerchantsShouldNotBeReturned($merchants)
    {
        $merchantList = array_filter(explode(',', $merchants));
        $flipped      = array_flip((array) $this->lastResponse);
        foreach ($merchantList as $ignored) {
            PHPUnit::assertArrayNotHasKey($ignored, $flipped);
        }
    }

    /**
     * @When I search for :term with :merchant filtered
     */
    public function iSearchForMerchantsWithFilters($term, $merchant)
    {
        $filtered = [];
        $merchant = explode(',', $merchant);
        $merchant = array_filter($merchant);
        $list     = [];
        foreach ($merchant as $name) {
            $name       = trim($name);
            $merchantId = array_search($name, $this->merchantMapping);
            PHPUnit::assertNotEquals(false, $merchant, "$name mapping not set");
            $list[] = $merchantId;
        }

        $result = $this->merchantRepository->findMerchants(self::DOMAIND_ID, [$term], $list);
        foreach ($result as $merchant) {
            $filtered[] = $merchant->getName();
        }
        $this->lastResponse = $filtered;
    }
}
