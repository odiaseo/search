<?php

namespace MapleSyrupGroup\Search\Services\Importer\Exceptions;

use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;

/**
 * API returned a strangely formatted message when retrieving data
 *
 * @package MapleSyrupGroup\Search\Services\Importer\Exceptions
 */
class UnexpectedApiResponseException extends \UnexpectedValueException
{
    protected $code = ExceptionCodes::CODE_UNEXPECTED_API_ERROR;
}
