<?php

namespace MapleSyrupGroup\Search\Http\Controllers;

use InvalidArgumentException;
use MapleSyrupGroup\QCommon\Exceptions\Exception;
use MapleSyrupGroup\QCommon\Exceptions\InvalidRequestException;
use MapleSyrupGroup\QCommon\Http\Controllers\Controller;
use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;
use MapleSyrupGroup\Search\Exceptions\Factory\HttpExceptionFactory;
use MapleSyrupGroup\Search\Exceptions\SearchException;

abstract class AbstractController extends Controller
{
    use PaginatedResponseTrait;

    /**
     * @param callable $function
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function processRequest(callable $function)
    {
        try {
            return call_user_func($function, $this);
        } catch (SearchException $exception) {
            throw (new HttpExceptionFactory($exception))->create();
        } catch (InvalidArgumentException $exception) {
            throw $this->exception(
                InvalidRequestException::class,
                $exception->getMessage(),
                $exception,
                ExceptionCodes::CODE_INVALID_REQUEST
            );
        }
    }
}
