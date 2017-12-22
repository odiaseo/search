<?php

namespace MapleSyrupGroup\Search\Services\Importer\Exceptions;

use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;

/**
 * Retried the specified amount of times already. Give up.
 *
 * @package MapleSyrupGroup\Search\Services\Importer\Exceptions
 */
class OutOfRetriesException extends \Exception
{
    protected $code = ExceptionCodes::CODE_OUT_OF_RETRIES;
}
