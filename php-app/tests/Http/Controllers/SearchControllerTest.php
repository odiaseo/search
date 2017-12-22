<?php

namespace MapleSyrupGroup\Search\Http\Controllers;

use Illuminate\Contracts\Events\Dispatcher;
use MapleSyrupGroup\QCommon\Exceptions\ApiExceptionFactory;
use MapleSyrupGroup\QCommon\Exceptions\InvalidRequestException;
use MapleSyrupGroup\Search\Http\Request\ApiRequest;
use MapleSyrupGroup\Search\Services\Merchants\Search;
use MapleSyrupGroup\Search\TestCase;
use Prophecy\Argument;

/**
 * SearchController integration tests.
 *
 * @group controller
 * @group integration
 */
class SearchControllerTest extends TestCase
{
    const DOMAIN = 1;

    /**
     * @var string
     */
    protected $baseUrl = 'http://search.app';

    public function setUp()
    {
        parent::setUp();

        config(['domain_id' => self::DOMAIN]);
    }

    /**
     * @dataProvider searchParameterProvider
     *
     * @param $language
     * @param $domainId
     * @param $searchTerm
     */
    public function testMerchantSearchEndpointCanPerformMerchantSearch($language, $domainId, $searchTerm)
    {
        config(['domain_id' => $domainId]);

        $sampleResult = [
            'strategy'             => 'exact_match',
            'attempted_strategies' => 'exact_match',
            'hits'                 => [
                'hits'  => [
                    [
                        '_source' => [
                            'id'   => 1,
                            'name' => 'argos',
                        ],
                    ],

                ],
                'total' => 1,
            ],
        ];

        $search  = $this->prophesize(Search::Class);
        $events  = $this->prophesize(Dispatcher::class);
        $request = $this->prophesize(ApiRequest::class);
        $factory = $this->prophesize(ApiExceptionFactory::class);

        $factory->buildException(Argument::cetera())->willReturn(new InvalidRequestException());

        $request->queryParam('language')->willReturn($language);
        $request->queryParam('search_term')->willReturn($searchTerm);
        $request->queryParam('status')->willReturn('1');
        $request->queryParam('category_id')->willReturn('1');
        $request->queryParam('debug')->willReturn(false);
        $request->queryParam('exclude_merchants')->willReturn([]);
        $request->queryParam('page')->willReturn('1');
        $request->queryParam('page_size')->willReturn('10');
        $request->queryParam('strategy')->willReturn(null);
        $request->query('in_store', '')->willReturn(1);
        $request->queryParam('sort_field')->willReturn('');
        $request->queryParam('sort_order')->willReturn('');

        $search->search(Argument::cetera())->willReturn($sampleResult);
        $events->fire(Argument::cetera())->willReturn(null);

        $controller = new SearchController($search->reveal(), $events->reveal());
        $controller->setExceptionFactory($factory->reveal());
        $controller->searchMerchant($request->reveal());
    }

    /**
     * @dataProvider searchParameterProvider
     *
     * @param $language
     * @param $domainId
     * @param $searchTerm
     */
    public function testMerchantSearchHandlesMerchantSearchWithZeroResults($language, $domainId, $searchTerm)
    {
        config(['domain_id' => $domainId]);

        $sampleResult = [
            'strategy'             => 'exact_match',
            'attempted_strategies' => 'exact_match',
            'hits'                 => [
                'hits'  => [],
                'total' => 0,
            ],
        ];

        $search  = $this->prophesize(Search::Class);
        $events  = $this->prophesize(Dispatcher::class);
        $request = $this->prophesize(ApiRequest::class);
        $factory = $this->prophesize(ApiExceptionFactory::class);

        $factory->buildException(Argument::cetera())->willReturn(new InvalidRequestException());

        $request->queryParam('language')->willReturn($language);
        $request->queryParam('search_term')->willReturn($searchTerm);
        $request->queryParam('status')->willReturn('1');
        $request->queryParam('category_id')->willReturn('1');
        $request->queryParam('debug')->willReturn(false);
        $request->queryParam('exclude_merchants')->willReturn([]);
        $request->queryParam('page')->willReturn('1');
        $request->queryParam('page_size')->willReturn('10');
        $request->queryParam('strategy')->willReturn(null);
        $request->query('in_store', '')->willReturn(1);
        $request->queryParam('sort_field')->willReturn('');
        $request->queryParam('sort_order')->willReturn('');

        $search->search(Argument::cetera())->willReturn($sampleResult);

        $controller = new SearchController($search->reveal(), $events->reveal());
        $controller->setExceptionFactory($factory->reveal());
        $controller->searchMerchant($request->reveal());
    }

    /**
     * @expectedException \MapleSyrupGroup\QCommon\Exceptions\InvalidRequestException
     */
    public function testMerchantSearchThrowsExceptionWithInvalidLanguage()
    {
        config(['domain_id' => self::DOMAIN]);
        $search  = $this->prophesize(Search::Class);
        $events  = $this->prophesize(Dispatcher::class);
        $request = $this->prophesize(ApiRequest::class);
        $factory = $this->prophesize(ApiExceptionFactory::class);

        $factory->buildException(Argument::cetera())->willReturn(new InvalidRequestException());

        $request->queryParam('category_id')->willReturn('1');
        $request->queryParam('language')->willReturn('italian');
        $request->queryParam('search_term')->willReturn('fashion');
        $request->queryParam('exclude_merchants')->willReturn([]);
        $request->queryParam('strategy')->willReturn([]);
        $request->queryParam('debug')->willReturn(false);
        $request->queryParam('status')->willReturn('1');
        $request->queryParam('page')->willReturn('1');
        $request->queryParam('page_size')->willReturn('10');
        $request->query('in_store', '')->willReturn(1);
        $request->queryParam('sort_field')->willReturn('');
        $request->queryParam('sort_order')->willReturn('');

        $controller = new SearchController($search->reveal(), $events->reveal());
        $controller->setExceptionFactory($factory->reveal());
        $controller->searchMerchant($request->reveal());
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Exceptions\Http\BaseHttpException
     */
    public function testMerchantSearchThrowsHttpExceptionWhenASearchExceptionOccurs()
    {
        config(['domain_id' => self::DOMAIN]);
        $search  = $this->prophesize(Search::Class);
        $events  = $this->prophesize(Dispatcher::class);
        $request = $this->prophesize(ApiRequest::class);
        $factory = $this->prophesize(ApiExceptionFactory::class);

        $factory->buildException(Argument::cetera())->willReturn(new Search\SearchCriteriaNotMetException());

        $request->queryParam('category_id')->willReturn('1');
        $request->queryParam('language')->willReturn('english');
        $request->queryParam('search_term')->willReturn('fashion');
        $request->queryParam('exclude_merchants')->willReturn([]);
        $request->queryParam('strategy')->willReturn([]);
        $request->queryParam('debug')->willReturn(false);
        $request->queryParam('status')->willReturn('1');
        $request->queryParam('page')->willReturn('1');
        $request->queryParam('page_size')->willReturn('10');
        $request->query('in_store', '')->willReturn(1);
        $request->queryParam('sort_field')->willReturn('');
        $request->queryParam('sort_order')->willReturn('');

        $search->search(Argument::cetera())->willThrow(new Search\SearchCriteriaNotMetException());

        $controller = new SearchController($search->reveal(), $events->reveal());
        $controller->setExceptionFactory($factory->reveal());
        $controller->searchMerchant($request->reveal());
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
