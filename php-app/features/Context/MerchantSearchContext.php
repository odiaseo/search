<?php

namespace MapleSyrupGroup\Search\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use MapleSyrupGroup\Search\Behat\Search\Merchant;
use MapleSyrupGroup\Search\Behat\Search\MerchantRepository;
use MapleSyrupGroup\Search\Services\Merchants\Factory\SearchFactory;
use PHPUnit_Framework_Assert as PHPUnit;

class MerchantSearchContext implements Context
{
    const DOMAIND_ID = 1;

    private $categoryMapping = [
        'hotels'        => 0,
        'accommodation' => 86,
        'flights'       => 89,
        'clothing'      => 21,
        'car insurance' => 71,
        'gambling'      => 42,
        'travel'        => '9',
        'insurance'     => '68',
        'footwear'      => '22',
        'dating'        => '41',
        'garden'        => '27',
        'music'         => '45',
        'toys & gifts'  => '16',
    ];

    /**
     * @var MerchantRepository
     */
    private $merchantRepository;

    /**
     * @var array
     */
    private $lastResponse;

    /**
     * @var int
     */
    private $pageSize = 40;

    /**
     * @var int
     */
    private $page = 1;

    /**
     * @var array
     */
    private $rawResponse;

    /**
     * @var string
     */
    private $sortField = '';

    /**
     * @var string
     */
    private $sortDirection = '';

    /**
     * @param MerchantRepository $merchantRepository
     */
    public function __construct(MerchantRepository $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * @Given the following merchants exist:
     */
    public function theFollowingMerchantsExist(TableNode $table)
    {
        // since we work with pre-defined fixtures, we only need to make sure all the merchants are there

        $merchants      = $table->getColumn(0);
        $foundMerchants = $this->merchantRepository->findMerchants(self::DOMAIND_ID, $merchants, [], 1, 1, false);

        $this->assertSameMerchants($merchants, $foundMerchants);
    }

    /**
     * @Given the following merchants exist with associated keywords:
     */
    public function theFollowingMerchantsExistWithAssociatedKeywords(TableNode $table)
    {
        $expectedMerchants = array_combine(
            array_map(function ($row) {
                return $row['merchant'];
            }, $table->getHash()),
            array_map(function ($row) {
                return $row['keyword'];
            }, $table->getHash())
        );

        $foundMerchants = $this->merchantRepository->findMerchants(
            self::DOMAIND_ID,
                array_keys($expectedMerchants),
                [],
            1,
                40,
                false
        );

        foreach ($foundMerchants as $merchant) {
            if (isset($expectedMerchants[$merchant->getName()])) {
                $keyword = $expectedMerchants[$merchant->getName()];
                if (!$merchant->hasKeyword($keyword)) {
                    throw new \LogicException(
                        sprintf(
                            'Expected the "%s" merchant to have the "%s" keyword, but it only has: "%s".',
                            $merchant,
                            $keyword,
                            implode(', ', $merchant->getKeywords())
                        )
                    );
                }
            }
        }

        foreach (array_keys($expectedMerchants) as $merchant) {
            PHPUnit::assertContains($merchant, $foundMerchants);
        }
    }

    /**
     * @When I search for :merchant
     */
    public function iSearchForAmazon($merchant)
    {
        $this->lastResponse = $this->merchantRepository
            ->findMerchants(self::DOMAIND_ID, [$merchant], [], $this->page, $this->pageSize, false,
                MerchantRepository::LANG_ENGLISH, '', $this->sortField, $this->sortDirection);
        $this->rawResponse  = current($this->merchantRepository->getRawResponse());
    }

    /**
     * @When I search for :merchant by id
     */
    public function iSearchForCategoryById($categoryName)
    {
        $categoryIds = array_filter(explode(',', $categoryName));
        array_walk($categoryIds, function (&$name) {
            $name = $this->categoryMapping[$name];
        });

        $this->lastResponse = $this->merchantRepository->findMerchantsByCategory(self::DOMAIND_ID, '', $categoryIds);
        $this->rawResponse  = current($this->merchantRepository->getRawResponse());
    }

    /**
     * @Then :merchant should be the only merchant returned
     */
    public function itShouldBeTheOnlyMerchantReturned($merchant)
    {
        PHPUnit::assertNotEmpty($this->lastResponse);
        PHPUnit::assertCount(1, $this->lastResponse);
        $list = [SearchFactory::STRATEGY_EXACT_MATCH, SearchFactory::STRATEGY_PREFIX_MATCH];
        $this->assertMerchantIsInResponse($merchant, $this->lastResponse);
        $this->assertSearchStragegy($list, $this->lastResponse);
    }

    /**
     * @Then /^"(?P<merchant>.*?)" should be (?:one|part) of the returned (?P<strategy>exact|best|fallback|category|prefix|name prefix) matches$/
     */
    public function shouldBeOnTheListOfMatches($merchant, $strategy)
    {
        $strategies = [
            'exact'       => SearchFactory::STRATEGY_EXACT_MATCH,
            'name prefix' => SearchFactory::STRATEGY_PREFIX_MATCH,
            'category'    => SearchFactory::STRATEGY_CATEGORY_EXACT_MATCH,
            'prefix'      => SearchFactory::STRATEGY_CATEGORY_PREFIX_MATCH,
            'best'        => SearchFactory::STRATEGY_MOST_RELEVANT,
            'fallback'    => SearchFactory::STRATEGY_LAST_RESORT,
        ];

        $merchants = false !== strpos($merchant, ',') ? array_map('trim', explode(',', $merchant)) : [$merchant];

        foreach ($merchants as $merchant) {
            $this->assertMerchantIsInResponse($merchant, $this->lastResponse);
        }
        $this->assertSearchStragegy($strategies[$strategy], $this->lastResponse);
    }

    /**
     * @Then :merchant should be in the specified order
     */
    public function merchantsShouldBeInTheSpecifiedOrder($merchant)
    {
        $merchants = false !== strpos($merchant, ',') ? array_map('trim', explode(',', $merchant)) : [$merchant];
        $results   = array_map(function ($hit) {
            return $hit->getName();
        }, $this->lastResponse);

        foreach ($merchants as $order => $name) {
            $message = sprintf('%s not returned in the correct order i result: %s', $name, implode(', ', $results));
            PHPUnit::assertSame(strtolower($name), strtolower($results[$order]), $message);
        }
    }

    /**
     * @Then /^the following merchants should be part of returned (?P<strategy>exact|best|fallback) matches:$/
     */
    public function theFollowingMerchantsShouldBePartOfReturnedMatches($strategy, TableNode $table)
    {
        foreach ($table->getColumn(0) as $merchant) {
            $this->shouldBeOnTheListOfMatches($merchant, $strategy);
        }
    }

    /**
     * @When I search for merchants by :searchTerm in order of popularity
     */
    public function iSearchForMerchantsByNameFilteredById($searchTerm)
    {
        PHPUnit::assertArrayHasKey($searchTerm, $this->categoryMapping, 'Category name not mapped to an ID');

        $this->lastResponse = $this->merchantRepository->findMerchantsByCategory(
            self::DOMAIND_ID,
            $searchTerm,
            [$this->categoryMapping[$searchTerm]]
        );
    }

    /**
     * @param string     $merchant
     * @param Merchant[] $response
     */
    private function assertMerchantIsInResponse($merchant, array $response)
    {
        PHPUnit::assertGreaterThan(0, count($response), 'There was at least one hit.');

        $merchants = array_map(function ($hit) {
            return $hit->getName();
        }, $response);

        if (!in_array($merchant, $merchants)) {
            PHPUnit::fail(
                sprintf(
                    'Merchant was not found in the search results: "%s". Found merchants: "%s".',
                    $merchant,
                    implode(', ', $merchants)
                )
            );
        }
    }

    /**
     * @param string     $strategy
     * @param Merchant[] $response
     */
    private function assertSearchStragegy($strategy, array $response)
    {
        $strategy = (array) $strategy;
        foreach ($response as $merchant) {
            PHPUnit::assertContains($merchant->getStrategy(), $strategy);
        }
    }

    /**
     * @param array      $merchantNames
     * @param Merchant[] $foundMerchants
     */
    private function assertSameMerchants($merchantNames, $foundMerchants)
    {
        $merchantNames  = array_unique(array_map('strtolower', $merchantNames));
        $foundMerchants = array_unique(array_map('strtolower', $foundMerchants));

        sort($merchantNames);
        sort($foundMerchants);

        PHPUnit::assertSame(
            $merchantNames,
            $foundMerchants,
            print_r(array_diff($merchantNames, $foundMerchants), true)
        );
    }

    /**
     * @Given I select a page size of :size
     */
    public function iSelectAPageSizeOf($size)
    {
        $this->pageSize = $size;
    }

    /**
     * @Given I select page :page
     */
    public function iSelectPage($page)
    {
        $this->page = $page;
    }

    /**
     * @When the total number of results are less than :totalResult
     */
    public function theTotalNumberOfResultsAreLessThanPageSize($totalResult)
    {
        PHPUnit::assertGreaterThan(0, $this->rawResponse['meta']['pagination']['total']);
        PHPUnit::assertLessThan($totalResult, $this->rawResponse['meta']['pagination']['total']);
    }

    /**
     * @Then I want page :page of the results to be returned
     */
    public function iWantSpecifiedPageOfTheResultsToBeReturned($page)
    {
        PHPUnit::assertEquals($page, $this->rawResponse['meta']['pagination']['current_page']);
    }

    /**
     * @Then with no pagination available
     */
    public function withNoPaginationAvailable()
    {
        PHPUnit::assertEquals(1, $this->rawResponse['meta']['pagination']['total_pages']);
        PHPUnit::assertEmpty($this->rawResponse['meta']['pagination']['links']);
    }

    /**
     * @When the total number of results are greater than :pageSize
     */
    public function theTotalNumberOfResultsAreGreaterThanPageSize($pageSize)
    {
        PHPUnit::assertGreaterThan((int) $pageSize, (int) $this->rawResponse['meta']['pagination']['total']);
    }

    /**
     * @Then I want a maximum of :numberOfResults results to be shown on the page
     */
    public function iWantAMaximumOfResultsToBeShownOnThePage($numberOfResults)
    {
        PHPUnit::assertLessThanOrEqual(
            (int) $numberOfResults,
            (int) $this->rawResponse['meta']['pagination']['per_page']
        );
    }

    /**
     * @Then with pagination available
     */
    public function withPaginationAvailable()
    {
        PHPUnit::assertGreaterThan(1, $this->rawResponse['meta']['pagination']['total_pages']);
        PHPUnit::assertNotEmpty($this->rawResponse['meta']['pagination']['links']);
    }

    /**
     * @Given :pageSize is greater than the :maximumSize
     */
    public function pageSizeIsGreaterThanMaximum($pageSize, $maximumSize)
    {
        PHPUnit::assertGreaterThan($maximumSize, $pageSize);
    }

    /**
     * @When I search for :keyword with the is-store filter set to :flag
     */
    public function iSearchForWithTheIsStoreFilterSetTo($keyword, $inStore)
    {
        $value              = ('yes' == strtolower($inStore)) ? 1 : 0;
        $this->lastResponse = $this->merchantRepository->findMerchants(
            self::DOMAIND_ID,
            [$keyword],
            [],
            $this->page,
            $this->pageSize,
            false,
            MerchantRepository::LANG_ENGLISH,
            $value
        );
    }

    /**
     * @Then All merchants found should have have the flag set to :flag
     */
    public function allMerchantsFoundShouldHaveHaveTheFlagSetTo($flag)
    {
        $value = ('yes' == strtolower($flag)) ? 1 : 0;

        /** @var Merchant $merchant */
        foreach ($this->lastResponse as $merchant) {
            PHPUnit::assertSame($value, $merchant->getIsInStore());
        }
    }

    /**
     * @Given I specify the sort order to be in :arg1 order of :arg2
     */
    public function specifyTheSortOrderToBeInOrderOf($sortDirection, $sortField)
    {
        $this->sortField     = $sortField;
        $this->sortDirection = $sortDirection;
    }
}
