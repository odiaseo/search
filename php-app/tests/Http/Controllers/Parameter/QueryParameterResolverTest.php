<?php

namespace MapleSyrupGroup\Search\Http\Controllers\Parameter;

use MapleSyrupGroup\Search\Http\Request\ApiRequest;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;
use Prophecy\Prophecy\ObjectProphecy;

class QueryParameterResolverTest extends \PHPUnit_Framework_TestCase
{
    const DOMAIN_ID = 200;

    const SEARCH_TERM = 'foo';

    /**
     * @var CategoryMerchantParameterResolver | MerchantSearchParameterResolver
     */
    private $resolver;

    /**
     * @var ApiRequest|ObjectProphecy
     */
    private $request;

    protected function setUp()
    {
        $this->request = $this->prophesize(ApiRequest::class);
        $this->request->queryParam('search_term')->willReturn(self::SEARCH_TERM);
        $this->request->queryParam('status')->willReturn(null);
        $this->request->queryParam('language')->willReturn(null);
        $this->request->queryParam('page')->willReturn(null);
        $this->request->queryParam('page_size')->willReturn(null);
        $this->request->queryParam('exclude_merchants')->willReturn(null);
        $this->request->queryParam('debug')->willReturn(null);
        $this->request->queryParam('category_id')->willReturn(null);
        $this->request->queryParam('strategy')->willReturn(null);
        $this->request->queryParam('sort_field')->willReturn('');
        $this->request->queryParam('sort_order')->willReturn('');
        $this->request->query('in_store', '')->willReturn(null);

        $this->resolver = new MerchantSearchParameterResolver();
    }

    public function testItConvertsRequestToQuery()
    {
        $query = $this->resolver->createQuery($this->request->reveal(), self::DOMAIN_ID);

        $this->assertInstanceOf(Query::class, $query);
        $this->assertSame(self::SEARCH_TERM, $query->getSearchTerm());
        $this->assertSame(self::DOMAIN_ID, $query->getDomainId());
        $this->assertSame(Query::LANGUAGE_ENGLISH, $query->getLanguage());
        $this->assertEmpty($query->getFilters());
        $this->assertSame(0, $query->getPage());
        $this->assertSame(0, $query->getPageSize());
        $this->assertSame([], $query->getExcludedMerchants());
        $this->assertFalse($query->isDebug());
    }

    public function testItIncludesStatusIfPresent()
    {
        $this->request->queryParam('status')->willReturn('active');

        $query = $this->resolver->createQuery($this->request->reveal(), self::DOMAIN_ID);

        $this->assertArraySubset(['status' => 'active'], $query->getFilters());
    }

    public function testItSetsTheLanguageIfPresent()
    {
        $this->request->queryParam('language')->willReturn(Query::LANGUAGE_FRENCH);

        $query = $this->resolver->createQuery($this->request->reveal(), self::DOMAIN_ID);

        $this->assertSame(Query::LANGUAGE_FRENCH, $query->getLanguage());
    }

    public function testItSetsPageDetails()
    {
        $this->request->queryParam('page')->willReturn(2);
        $this->request->queryParam('page_size')->willReturn(5);

        $query = $this->resolver->createQuery($this->request->reveal(), self::DOMAIN_ID);

        $this->assertSame(2, $query->getPage());
        $this->assertSame(5, $query->getPageSize());
    }

    public function testItSetsExcludedMerchants()
    {
        $this->request->queryParam('exclude_merchants')->willReturn('13,42');

        $query = $this->resolver->createQuery($this->request->reveal(), self::DOMAIN_ID);

        $this->assertSame(['13', '42'], $query->getExcludedMerchants());
    }

    public function testItSetsTheDebugMode()
    {
        $this->request->queryParam('debug')->willReturn(true);

        $query = $this->resolver->createQuery($this->request->reveal(), self::DOMAIN_ID);

        $this->assertTrue($query->isDebug());
    }

    /** @dataProvider instoreFilterProvider */
    public function testInstoreFilterCanBeRetrievedFromRequest($storeValue, $expectedValue)
    {
        $request = $this->prophesize(ApiRequest::class);
        $request->query('in_store', '')->willReturn($storeValue);
        $param = $this->prophesize(SortParameter::class)->reveal();

        $query = $this->resolver->setInStoreFilter(new Query('', 1, new SortParameter()), $request->reveal(), $param);

        $this->assertSame($expectedValue, $query->getIsInStore());
    }

    public function instoreFilterProvider()
    {
        return [
            ['', null],
            [null, null],
            [0, 0],
            ['off', 0],
            ['0', 0],
            ['false', 0],
            ['no', 0],
            [1, 1],
            ['1', 1],
            ['true', 1],
            ['yes', 1],
            ['on', 1],
        ];
    }
}
