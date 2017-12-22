<?php

namespace MapleSyrupGroup\Search\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MapleSyrupGroup\QCommon\Exceptions\BaseException;
use MapleSyrupGroup\Search\Exceptions\Http\BaseHttpException;
use MapleSyrupGroup\Search\Exceptions\Stubs\HandlerStub;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\InvalidUrlException;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\NoMerchantFoundException;
use MapleSyrupGroup\Search\Services\Merchants\Search\SearchCriteriaNotMetException;
use Psr\Log\LoggerInterface;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param \Exception $exception
     * @param bool $showTrace
     * @param int $httpCode
     *
     * @dataProvider errorDataProvider
     */
    public function testErrorResponseGeneratesPayload($exception, $showTrace, $httpCode)
    {
        $logger  = $this->prophesize(LoggerInterface::class);
        $request = $this->prophesize(Request::class);
        $handler = new HandlerStub($logger->reveal());

        $handler->setShowTrace($showTrace);
        if ($exception instanceof SearchHttpException) {
            $exception->setHttpStatusCode($httpCode);
        }
        $response = $handler->render($request->reveal(), $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = $response->getData(true);

        $this->assertArrayHasKey('errors', $data, print_r($data, true));
        $this->assertInternalType('array', $data['errors']);
        $this->assertGreaterThanOrEqual(1, count($data['errors']));
        $this->assertSame($exception->getMessage(), $data['errors'][0]['message']);

        if ($showTrace) {
            $this->assertArrayHasKey('traceback', $data['errors'][0]);
        } else {
            $this->assertArrayNotHasKey('traceback', $data['errors'][0]);
        }
    }

    /**
     * @param BaseHttpException $exception
     * @param int $httpCode
     * @param true $expected
     *
     * @dataProvider errorReportDataProvider
     */
    public function testErrorIsReported($exception, $httpCode, $expected)
    {
        $logger = $this->prophesize(LoggerInterface::class);

        $logger->error($exception)->willReturn(null);
        if ($exception instanceof SearchHttpException) {
            $exception->setHttpStatusCode($httpCode);
        }
        $handler  = new HandlerStub($logger->reveal());
        $response = $handler->report($exception);

        $this->assertSame($expected, $response);
    }

    public function errorDataProvider()
    {
        return [
            [new \Exception(), false, '100'],
            [new \Exception('message', 100, new \Exception()), true, '101'],
            [new SearchCriteriaNotMetException('invalid search criteria'), false, 401],
            [new SearchCriteriaNotMetException('invalid search criteria'), false, 500],
            [new NoMerchantFoundException('invalid merchant'), true, 404],
            [new NoMerchantFoundException('no status code'), false, 100],
            [new BaseException('invalid', 000), false, '120'],
            [new BaseHttpException('invalid', 000, new InvalidUrlException()), false, '120'],
            [new BaseHttpException('invalid', 000, new \Exception()), false, '120'],
            [new BaseHttpException('invalid', 000, new \Exception()), false, 404],
            [new BaseHttpException('invalid', 000, new SearchCriteriaNotMetException()), false, 400],
            [new BaseHttpException('invalid', 000, new NoMerchantFoundException()), false, 404],
            [(new BaseException('invalid', 000))->addParameterError(100, 'error', 'id'), true, 100],
        ];
    }

    public function errorReportDataProvider()
    {
        return [
            [new \Exception('invalid', 000, new \Exception()), 404, true],
            [new BaseHttpException('invalid', 000, new \Exception()), 404, true],
            [new BaseHttpException('invalid', 000, new SearchCriteriaNotMetException()), 400, false],
            [new BaseHttpException('invalid', 000, new SearchCriteriaNotMetException()), 500, true],
            [new BaseHttpException('invalid', 000, new NoMerchantFoundException()), 404, false],
            [new BaseHttpException('invalid', 000, new NoMerchantFoundException()), 401, true],
        ];
    }
}
