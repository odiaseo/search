<?php

namespace MapleSyrupGroup\Search\Services\Merchants;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    const SEARCH_TERM = 'amazon';

    const DOMAIN_ID = 1;

    public function testItIsADomainQuery()
    {
        $this->assertInstanceOf(DomainQuery::class, new Query(self::SEARCH_TERM, self::DOMAIN_ID, new SortParameter()));
    }

    public function testItExposesTheSearchTermAndDomainId()
    {
        $query = new Query(self::SEARCH_TERM, self::DOMAIN_ID, new SortParameter());

        $this->assertSame(self::SEARCH_TERM, $query->getSearchTerm());
        $this->assertSame(self::DOMAIN_ID, $query->getDomainId());
    }

    public function testItLowerCasesTheSearchTerm()
    {
        $term  = 'Ama Zon';
        $query = new Query($term, self::DOMAIN_ID, new SortParameter());

        $this->assertSame($term, $query->getSearchTerm());
    }

    public function testItRegistersTheAdultFilter()
    {
        $query = new Query(self::SEARCH_TERM, self::DOMAIN_ID, new SortParameter());
        $query->setShowAdultMerchants(true);

        $this->assertHasFilter('adult', true, $query->getFilters());
    }

    public function testItRegistersTheGamblingFilter()
    {
        $query = new Query(self::SEARCH_TERM, self::DOMAIN_ID, new SortParameter());
        $query->setShowGamblingMerchants(true);

        $this->assertHasFilter('gambling', true, $query->getFilters());
    }

    public function testItRegistersTheStatusFilter()
    {
        $query = new Query(self::SEARCH_TERM, self::DOMAIN_ID, new SortParameter());
        $query->setStatus('active');

        $this->assertHasFilter('status', 'active', $query->getFilters());
    }

    public function testItSetsThePageAndPageSize()
    {
        $query = new Query(self::SEARCH_TERM, self::DOMAIN_ID, new SortParameter());
        $query->setPage(2);
        $query->setPageSize(15);

        $this->assertSame(2, $query->getPage());
        $this->assertSame(15, $query->getPageSize());
    }

    public function testMaximumPageSizeIsNotExceeded()
    {
        $query = new Query(self::SEARCH_TERM, self::DOMAIN_ID, new SortParameter());
        $query->setPage(2);
        $query->setPageSize(15000);

        $this->assertSame(2, $query->getPage());
        $this->assertSame(Query::MAXIMUM_PAGE_SIZE, $query->getPageSize());
    }

    public function testItExposesTheLanguage()
    {
        $query = new Query(self::SEARCH_TERM, self::DOMAIN_ID, new SortParameter());
        $query->setLanguage('french');

        $this->assertSame('french', $query->getLanguage());
    }

    /**
     * @param $merchantList
     * @param $expectedList
     *
     * @dataProvider excludedMerchantDataProvider
     */
    public function testItRegistersExcludedMerchantFilter($merchantList, $expectedList)
    {
        $query = new Query(self::SEARCH_TERM, self::DOMAIN_ID, new SortParameter());
        $query->setExcludedMerchants($merchantList);

        $this->assertSame($expectedList, $query->getExcludedMerchants());
    }

    public function excludedMerchantDataProvider()
    {
        return [
            [[], []],
            [null, []],
            ['0,00,000,0000,900', ['900']],
            [[0, '0', false, ''], []],
            [123, [123]],
            ['123,,789,', ['123', '789']],
            ['123,456,789', ['123', '456', '789']],
            [',<script>alert()</script>,123', ['123']],
            [(object) [123], [123]],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testItThrowsAnInvalidArgumentExceptionIfLanguageIsInvalid()
    {
        $query = new Query(self::SEARCH_TERM, self::DOMAIN_ID, new SortParameter());
        $query->setLanguage('foo');
    }

    public function testItIsNotInDebugModeByDefault()
    {
        $query = new Query(self::SEARCH_TERM, self::DOMAIN_ID, new SortParameter());

        $this->assertFalse($query->isDebug());
    }

    public function testItCanBePutIntoDebugMode()
    {
        $query = new Query(self::SEARCH_TERM, self::DOMAIN_ID, new SortParameter());
        $query->setDebug(true);

        $this->assertTrue($query->isDebug());
    }

    public function testItCanBeFilteredByIsInStore()
    {
        $query = new Query(self::SEARCH_TERM, self::DOMAIN_ID, new SortParameter());
        $query->setIsInStore(1);

        $this->assertHasFilter('is_in_store', 1, $query->getFilters());
    }

    private function assertHasFilter($name, $value, $filters)
    {
        $this->assertArrayHasKey(
            $name,
            $filters,
            sprintf('Expected the "%s" filter to be registered.', $name)
        );
        $this->assertSame(
            $value,
            $filters[$name],
            sprintf('Expected the "%s" filter to be set to "%s" but got "%s".', $name, $value, $filters[$name])
        );
    }
}
