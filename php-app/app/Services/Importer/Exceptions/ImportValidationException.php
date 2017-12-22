<?php

namespace MapleSyrupGroup\Search\Services\Importer\Exceptions;

use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;

/**
 * Import didn't validate when we tested it
 *
 * @package MapleSyrupGroup\Search\Services\Importer\Exceptions
 */
class ImportValidationException extends \UnexpectedValueException
{
    protected $code = ExceptionCodes::CODE_INVALID_IMPORT;
}
