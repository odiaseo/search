<?php

namespace MapleSyrupGroup\Search\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use MapleSyrupGroup\QCommon\Exceptions\ApiExceptionFactory;
use MapleSyrupGroup\QCommon\Exceptions\InvalidRequestException;
use MapleSyrupGroup\QCommon\Http\ResponseFactory;
use MapleSyrupGroup\Search\Console\Kernel;
use MapleSyrupGroup\Search\Http\Request\ApiRequest;
use MapleSyrupGroup\Search\Services\IndexStatusTracker\IndexStatusTracker;
use Prophecy\Argument;

/**
 * SearchIndexController tests.
 *
 * @group controller
 */
class SearchIndexControllerTest extends \PHPUnit_Framework_TestCase
{
    const DOMAIN_ID = 1;

    public function setUp()
    {
        config(['domain_id' => self::DOMAIN_ID]);
    }

    /**
     * @expectedException \MapleSyrupGroup\QCommon\Exceptions\InvalidRequestException
     */
    public function testEndpointReturnsExceptionWhenCheckingTheStatusOfAnInvalidIndex()
    {
        $request         = $this->prophesize(ApiRequest::class);
        $factory         = $this->prophesize(ApiExceptionFactory::class);
        $responseFactory = $this->prophesize(ResponseFactory::class);

        $statusService = $this->prophesize(IndexStatusTracker::class);
        $statusService->getStatus(\Prophecy\Argument::cetera())->willThrow(new InvalidRequestException());

        $factory->buildException(\Prophecy\Argument::cetera())->willReturn(new InvalidRequestException());
        $request->pathParam('id')->willReturn('1');

        $controller = new SearchIndexController($statusService->reveal());
        $controller->setResponseFactory($responseFactory->reveal());
        $controller->setExceptionFactory($factory->reveal());
        $controller->getMerchantIndexStatus($request->reveal());
    }

    /**
     * @expectedException \MapleSyrupGroup\QCommon\Exceptions\InvalidRequestException
     */
    public function testEndpointCatchesExceptionWhenQueuingIndexBuild()
    {
        $factory = $this->prophesize(ApiExceptionFactory::class);
        $kernel  = $this->prophesize(Kernel::class);
        $status  = $this->prophesize(IndexStatusTracker::class);

        $status->lock(\Prophecy\Argument::cetera())->willThrow(new InvalidRequestException());
        $status->getUniqueIdentifier()->willReturn(uniqid('123'));
        $factory->buildException(\Prophecy\Argument::cetera())->willReturn(new InvalidRequestException());

        $controller = new SearchIndexController($status->reveal());

        $controller->setExceptionFactory($factory->reveal());
        $controller->updateIndexMerchant($kernel->reveal());
    }

    public function testSearchControllerCanQueueIndexBuild()
    {
        $factory         = $this->prophesize(ApiExceptionFactory::class);
        $kernel          = $this->prophesize(Kernel::class);
        $status          = $this->prophesize(IndexStatusTracker::class);
        $responseFactory = $this->prophesize(ResponseFactory::class);

        $status->lock(\Prophecy\Argument::cetera())->shouldBeCalled();
        $status->getStatus(\Prophecy\Argument::cetera())->shouldBeCalled();
        $status->getUniqueIdentifier(\Prophecy\Argument::cetera())->shouldBeCalled();

        $kernel->queue(Argument::cetera())->willReturn(null);
        $factory->buildException(\Prophecy\Argument::cetera())->willReturn(new InvalidRequestException());

        $controller = new SearchIndexController($status->reveal());
        $controller->setExceptionFactory($factory->reveal());
        $controller->setResponseFactory($responseFactory->reveal());

        $controller->updateIndexMerchant($kernel->reveal());
    }

    public function testSearchControllerCanReturnIndexStatus()
    {
        $request         = $this->prophesize(ApiRequest::class);
        $factory         = $this->prophesize(ApiExceptionFactory::class);
        $responseFactory = $this->prophesize(ResponseFactory::class);
        $statusService   = $this->prophesize(IndexStatusTracker::class);

        $statusService->getStatus(self::DOMAIN_ID, 1)->willReturn([]);
        $factory->buildException(\Prophecy\Argument::cetera())->willReturn(new ModelNotFoundException());
        $request->pathParam('id')->willReturn('1');

        $controller = new SearchIndexController($statusService->reveal());
        $controller->setResponseFactory($responseFactory->reveal());
        $controller->setExceptionFactory($factory->reveal());

        $controller->getMerchantIndexStatus($request->reveal());
    }

    public function searchParameterProvider()
    {
        return [
            ['english', 1, 'fashion'],
            ['french', 200, 'la poste'],
        ];
    }
}
