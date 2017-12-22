<?php

namespace MapleSyrupGroup\Search\Exceptions\Http;

use MapleSyrupGroup\Search\Exceptions\SearchHttpException;

/**
 * Exceptions aware of HTTP status code
 */
class BaseHttpException extends \LogicException implements SearchHttpException
{
    /**
     * @var int
     */
    private $httpStatusCode = 400;

    /**
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * @inheritdoc
     */
    public function setHttpStatusCode($httpStatusCode)
    {
        $this->httpStatusCode = (int)$httpStatusCode;

        return $this;
    }
}
