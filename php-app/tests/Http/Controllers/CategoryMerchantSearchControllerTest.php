<?php

namespace MapleSyrupGroup\Search\Http\Controllers;

use MapleSyrupGroup\QCommon\Exceptions\ApiExceptionFactory;
use MapleSyrupGroup\QCommon\Exceptions\InvalidRequestException;
use MapleSyrupGroup\Search\Http\Request\ApiRequest;
use MapleSyrupGroup\Search\Services\Merchants\Search;
use MapleSyrupGroup\Search\Services\Merchants\Search\CategoryExactMatchSearch;
use MapleSyrupGroup\Search\Services\Merchants\Search\SearchCriteriaNotMetException;
use MapleSyrupGroup\Search\TestCase;

/**
 * Class CategoryMerchantSearchControllerTest.
 *
 * @group controller
 */
class CategoryMerchantSearchControllerTest extends TestCase
{
    const DOMAIN = 1;

    /**
     * @var string
     */
    protected $baseUrl = 'http://search.app';

    /**
     * @dataProvider searchParameterProvider
     *
     * @param $language
     * @param $domainId
     * @param $searchTerm
     */
    public function testCategoryMerchantSearchEndpointCanPerformMerchantSearch($language, $domainId, $searchTerm)
    {
        config(['domain_id' => $domainId]);

        $sampleResult = [
            'strategy'             => 'exact_match',
            'attempted_strategies' => 'exact_match',
            'hits'                 => [
                'hits'  => [
                    [
                        'id'   => 1,
                        'name' => 'argos',
                    ],

                ],
                'total' => 1,
            ],
        ];

        $categorySearch  = $this->prophesize(CategoryExactMatchSearch::Class);
        $request         = $this->prophesize(ApiRequest::class);
        $factory         = $this->prophesize(ApiExceptionFactory::class);

        $factory->buildException(\Prophecy\Argument::cetera())->willReturn(new InvalidRequestException());

        $request->queryParam('language')->willReturn($language);
        $request->queryParam('search_term')->willReturn($searchTerm);
        $request->queryParam('category_id')->willReturn('1');
        $request->queryParam('debug')->willReturn(false);
        $request->queryParam('page')->willReturn('active');
        $request->queryParam('page_size')->willReturn('10');
        $request->queryParam('sort_field')->willReturn('relevance');
        $request->queryParam('sort_order')->willReturn('desc');

        $categorySearch->search(\Prophecy\Argument::cetera())->willReturn($sampleResult);

        $controller = new CategoryMerchantSearchController($categorySearch->reveal());
        $controller->setExceptionFactory($factory->reveal());
        $controller->findMerchantsByCategory($request->reveal());
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Exceptions\Http\BaseHttpException
     */
    public function testCategoryMerchantSearchThrowsExceptionWithInvalidSearchResults()
    {
        config(['domain_id' => self::DOMAIN]);
        $search  = $this->prophesize(CategoryExactMatchSearch::class);
        $request = $this->prophesize(ApiRequest::class);
        $factory = $this->prophesize(ApiExceptionFactory::class);

        $factory->buildException(\Prophecy\Argument::cetera())->willReturn(new InvalidRequestException());

        $request->queryParam('category_id')->willReturn('1');
        $request->queryParam('language')->willReturn('english');
        $request->queryParam('search_term')->willReturn('fashion');
        $request->queryParam('debug')->willReturn(false);
        $request->queryParam('page')->willReturn('1');
        $request->queryParam('page_size')->willReturn('20');
        $request->queryParam('sort_field')->willReturn('');
        $request->queryParam('sort_order')->willReturn('');

        $search->search(\Prophecy\Argument::cetera())->willThrow(new SearchCriteriaNotMetException());

        $controller = new CategoryMerchantSearchController($search->reveal());
        $controller->setExceptionFactory($factory->reveal());
        $controller->findMerchantsByCategory($request->reveal());
    }

    /**
     * @expectedException \MapleSyrupGroup\QCommon\Exceptions\InvalidRequestException
     */
    public function testCategoryMerchantSearchThrowsExceptionWithInvalidQueryParameter()
    {
        config(['domain_id' => self::DOMAIN]);
        $search  = $this->prophesize(CategoryExactMatchSearch::Class);
        $request = $this->prophesize(ApiRequest::class);
        $factory = $this->prophesize(ApiExceptionFactory::class);

        $factory->buildException(\Prophecy\Argument::cetera())->willReturn(new InvalidRequestException());

        $request->queryParam('category_id')->willReturn('1');
        $request->queryParam('language')->willReturn('english');
        $request->queryParam('search_term')->willReturn('fashion');
        $request->queryParam('debug')->willReturn(false);
        $request->queryParam('page')->willReturn('1');
        $request->queryParam('page_size')->willThrow(new \InvalidArgumentException());
        $request->queryParam('sort_field')->willReturn('');
        $request->queryParam('sort_order')->willReturn('');

        $controller = new CategoryMerchantSearchController($search->reveal());
        $controller->setExceptionFactory($factory->reveal());
        $controller->findMerchantsByCategory($request->reveal());
    }

    public function searchParameterProvider()
    {
        return [
            ['english', 1, 'fashion'],
            ['french', 200, 'la poste'],
        ];
    }

    public function tearDown()
    {
        restore_error_handler();
    }
}
