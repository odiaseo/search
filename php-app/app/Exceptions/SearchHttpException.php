<?php

namespace MapleSyrupGroup\Search\Exceptions;

/**
 * Exceptions thrown in Search MS that are aware of HTTP status code
 */
interface SearchHttpException extends SearchException
{
    /**
     * @return int mixed
     */
    public function getHttpStatusCode();

    /**
     * @param int $statusCode
     *
     * @return SearchHttpException
     */
    public function setHttpStatusCode($statusCode);
}
