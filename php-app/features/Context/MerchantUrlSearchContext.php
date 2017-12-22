<?php

namespace MapleSyrupGroup\Search\Behat\Context;

use Behat\Behat\Context\Context;
use MapleSyrupGroup\QCommon\Enum\DomainEnum;
use MapleSyrupGroup\Search\Behat\Search\MerchantRepository;
use MapleSyrupGroup\Search\Behat\Search\MerchantRepository as TestMerchantRepository;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Merchant;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\NoMerchantFoundException;

class MerchantUrlSearchContext implements Context
{
    /**
     * @var TestMerchantRepository
     */
    private $merchantRepository;

    /**
     * @var Merchant|null
     */
    private $foundMerchant;

    /**
     * @param TestMerchantRepository $merchantRepository
     */
    public function __construct(TestMerchantRepository $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * @Transform :rules
     */
    public function transformUrlRules($rules)
    {
        return array_map('trim', explode(',', $rules));
    }

    /**
     * @Given I have the Shoop toolbar enabled in my web browser
     */
    public function iHaveTheShoopToolbarEnabledInMyWebBrowser()
    {
        // nothing here, this step only exists to build the story
    }

    /**
     * @Given the :merchant merchant is on Shoop with cashback available for the following url rules: :rules
     */
    public function theMerchantIsOnShoopWithCashbackAvailableForTheFollowingUrlRules($merchant, $rules)
    {
        $merchant = $this->merchantRepository->findMerchant(
            DomainEnum::DOMAIN_ID_SHOOP,
            $merchant,
            1,
            1,
            MerchantRepository::LANG_FRENCH,
            false
        );

        \PHPUnit_Framework_Assert::assertArraySubset(
            $rules,
            $merchant->getLinks(),
            false,
            sprintf(
                'Expected url rules: "%s" but found: "%s".',
                implode(', ', $rules),
                implode(', ', $merchant->getLinks())
            )
        );
    }

    /**
     * @When I navigate to :url
     */
    public function iNavigateTo($url)
    {
        try {
            $this->foundMerchant = $this->merchantRepository->getByLink(DomainEnum::DOMAIN_ID_SHOOP, $url);
        } catch (NoMerchantFoundException $e) {
            // ignored, we will assert if the property was set in the next step
        }
    }

    /**
     * @Then I should be notified about cashback available for this site
     */
    public function iShouldBeNotifiedAboutCashbackAvailableForThisSite()
    {
        \PHPUnit_Framework_Assert::assertInstanceOf(Merchant::class, $this->foundMerchant);
    }

    /**
     * @Then I should see that cashback is not available for the site
     */
    public function iShouldSeeThatCashbackIsNotAvailableForTheSite()
    {
        \PHPUnit_Framework_Assert::assertNull($this->foundMerchant);

    }
}
