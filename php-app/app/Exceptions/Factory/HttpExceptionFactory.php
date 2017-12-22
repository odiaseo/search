<?php

namespace MapleSyrupGroup\Search\Exceptions\Factory;

use Exception;
use InvalidArgumentException;
use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;
use MapleSyrupGroup\Search\Exceptions\Handler;
use MapleSyrupGroup\Search\Exceptions\Http\BaseHttpException;
use MapleSyrupGroup\Search\Exceptions\SearchException;

/**
 * Create HTTP Status aware exception.
 */
class HttpExceptionFactory
{
    /**
     * @var SearchException | Exception
     */
    private $exception;

    /**
     * HttpExceptionFactory constructor.
     *
     * @param SearchException $searchException
     */
    public function __construct(SearchException $searchException)
    {
        if (!$searchException instanceof Exception) {
            throw new InvalidArgumentException(
                'argument must be an instance of \Exception',
                ExceptionCodes::CODE_INVALID_ARGUMENT
            );
        }

        $this->exception = $searchException;
    }

    /**
     * @return BaseHttpException
     */
    public function create()
    {
        $class = get_class($this->exception);

        $exception = new BaseHttpException(
            $this->exception->getMessage(),
            $this->exception->getCode(),
            $this->exception
        );

        if (array_key_exists($class, Handler::$exceptionMapping)) {
            $exception->setHttpStatusCode(Handler::$exceptionMapping[$class]);
        }

        return $exception;
    }
}
