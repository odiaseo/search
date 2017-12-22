<?php

namespace MapleSyrupGroup\Search\Exceptions;

use Exception;
use MapleSyrupGroup\QCommon\Exceptions\BaseException;
use MapleSyrupGroup\QCommon\Exceptions\Handler as ExceptionHandler;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\InvalidUrlException;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\NoMerchantFoundException;
use MapleSyrupGroup\Search\Services\Merchants\Search\SearchCriteriaNotMetException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * Map exceptions to status codes
     *
     * @var array
     */
    public static $exceptionMapping = [
        InvalidUrlException::class           => 400,
        SearchCriteriaNotMetException::class => 400,
        NoMerchantFoundException::class      => 404,
        HttpException::class                 => 404,
    ];

    /**
     * @inheritdoc
     */
    public function report(Exception $exception)
    {
        if ($this->shouldReport($exception)) {
            $this->log->error($exception);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function overrideErrorMessage(\Exception $exception, array $errorMessage)
    {
        return $errorMessage;
    }

    /**
     * {@inheritdoc}
     */
    protected function determineStatusCode(\Exception $exception)
    {
        /** @var SearchHttpException | BaseException $exception */
        if ($exception instanceof SearchHttpException) {
            return $exception->getHttpStatusCode();
        }

        return parent::determineStatusCode($exception);
    }

    /**
     * {@inheritdoc}
     */
    protected function shouldntReport(Exception $exception)
    {
        /* @var SearchHttpException | BaseException $exception */
        if (!($exception instanceof SearchHttpException && $exception->getPrevious())) {
            return parent::shouldntReport($exception);
        }

        $className = get_class($exception->getPrevious());

        if (!array_key_exists($className, self::$exceptionMapping)) {
            return false;
        }

        if (self::$exceptionMapping[$className] !== $exception->getHttpStatusCode()) {
            return false;
        }

        return true;
    }
}
