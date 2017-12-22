<?php

namespace MapleSyrupGroup\Search\Http\Controllers;

use MapleSyrupGroup\QCommon\Enum\DomainEnum;
use MapleSyrupGroup\Search\Http\Request\ApiRequest;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\LinkQuery;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Merchants;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\NoMerchantFoundException;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Stubs\MerchantFactory;
use MapleSyrupGroup\Search\Services\Merchants\Search\SearchCriteriaNotMetException;
use MapleSyrupGroup\Search\TestCase;
use Prophecy\Argument;

/**
 * @group integration
 */
class FindMerchantByLinkControllerTest extends TestCase
{
    const DOMAIN_ID = DomainEnum::DOMAIN_ID_SHOOP;

    const MATCHING_LINK = 'http://www.rugbycenter.fr/foo/bar';

    const NOT_MATCHING_LINK = 'http://store.rugbycenter.fr/foo/bar';

    /**
     * @var string
     */
    protected $baseUrl = 'http://search.app';

    public function setUp()
    {
        parent::setUp();

        config(['domain_id' => self::DOMAIN_ID]);
    }

    public function testItReturnsASingleMerchantIfFound()
    {
        $merchant  = MerchantFactory::withDefaults();
        $merchants = $this->prophesize(Merchants::class);
        $request   = $this->prophesize(ApiRequest::class);

        $merchants->getByLink(new LinkQuery(self::MATCHING_LINK, self::DOMAIN_ID))->willReturn($merchant);
        $request->queryParam('link')->willReturn(self::MATCHING_LINK);

        $controller = new FindMerchantByLinkController($merchants->reveal());
        $response   = $controller->findMerchant($request->reveal());
        $this->assertSame($merchant, $response);
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Exceptions\Http\BaseHttpException
     * @expectedExceptionMessage Merchant not found
     */
    public function testItReturnsA404IfMerchantIsNotFound()
    {
        $merchants = $this->prophesize(Merchants::class);
        $request   = $this->prophesize(ApiRequest::class);
        $merchants->getByLink(Argument::any())->willThrow(new NoMerchantFoundException('Merchant not found'));

        $request->queryParam('link')->willReturn('amazon.com');

        $controller = new FindMerchantByLinkController($merchants->reveal());
        $controller->findMerchant($request->reveal());
    }

    /**
     * Until we can easily build the index during the tests for data we set up
     * this test relies on previously built index for the servicetest environment.
     *
     * @group elasticsearch-index
     */
    public function testItIsWiredUpByTheFramework()
    {
        $merchants = $this->prophesize(Merchants::class);
        $request   = $this->prophesize(ApiRequest::class);

        $merchants->getByLink(new LinkQuery('amazon.com', self::DOMAIN_ID))->shouldBeCalled();
        $request->queryParam('link')->willReturn('amazon.com');

        $controller = new FindMerchantByLinkController($merchants->reveal());
        $controller->findMerchant($request->reveal());
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Exceptions\Http\BaseHttpException
     * @group elasticsearch-index
     */
    public function testBaseHttpExceptionIsThrownWhenASearchExceptionOccurs()
    {
        $merchants = $this->prophesize(Merchants::class);
        $request   = $this->prophesize(ApiRequest::class);

        $merchants->getByLink(Argument::any())->willThrow(new SearchCriteriaNotMetException());
        $request->queryParam('link')->willReturn('amazon.com');

        $controller = new FindMerchantByLinkController($merchants->reveal());
        $controller->findMerchant($request->reveal());
    }

    public function tearDown()
    {
        restore_error_handler();
    }
}
