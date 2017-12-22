<?php

namespace MapleSyrupGroup\Search\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use MapleSyrupGroup\Search\Behat\Search\Merchant;
use MapleSyrupGroup\Search\Behat\Search\MerchantRepository;
use PHPUnit_Framework_Assert as PHPUnit;

class MerchantRatesTextSearchContext implements Context
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
     * @param MerchantRepository $merchantRepository
     */
    public function __construct(MerchantRepository $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * @Then :merchant should be returned
     *
     * @param $merchant
     */
    public function itShouldBeReturned($merchant)
    {
        $foundMerchants = $this->getFoundMerchants();

        PHPUnit::assertNotEmpty($foundMerchants);
        PHPUnit::assertGreaterThanOrEqual(1, count($foundMerchants));
        PHPUnit::assertArrayHasKey(
            $merchant,
            $foundMerchants,
            sprintf('Found merchants: "%s".', implode(', ', array_keys($foundMerchants)))
        );
    }

    /**
     * @Given the following terms are not merchant exact matches:
     */
    public function theFollowingMerchantsDoNotExist(TableNode $table)
    {
        $merchants      = $table->getColumn(0);
        $foundMerchants = $this->merchantRepository->findMerchants(self::DOMAIND_ID, $merchants);

        sort($foundMerchants);

        PHPUnit::assertEmpty($foundMerchants);
    }

    /**
     * @When I search for :text as rates text
     *
     * @param $text
     */
    public function iSearchUsingRatesText($text)
    {
        $this->lastResponse = $this->merchantRepository->search(self::DOMAIND_ID, $text);
    }

    /**
     * @Given the merchant :merchant rates text description contains :text
     *
     * @param string $merchant
     * @param string $text
     */
    public function theMerchantRatesTextDescriptionContains($merchant, $text)
    {
        $foundRatesText = $this->merchantRepository->findMerchant(
            self::DOMAIND_ID,
            $merchant,
            1,
            1,
            MerchantRepository::LANG_ENGLISH,
            false)
            ->getRatesText();
        $foundRatesText = current($foundRatesText);
        PHPUnit::assertContains($text, $foundRatesText, print_r([$text, $foundRatesText], true));
    }

    /**
     * @Then the following :merchant should not be returned
     */
    public function theFollowingShouldNotBeReturned($merchant)
    {
        $merchantList   = array_filter(explode(',', $merchant));
        $foundMerchants = $this->getFoundMerchants();

        foreach ($merchantList as $ignored) {
            PHPUnit::assertArrayNotHasKey($ignored, $foundMerchants);
        }
    }

    /**
     * @return Merchant[] indexed by merchant name
     */
    private function getFoundMerchants()
    {
        $return = [];

        foreach ($this->lastResponse as $merchant) {
            $return[$merchant->getName()] = $merchant;
        }

        return $return;
    }
}
