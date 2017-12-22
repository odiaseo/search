<?php

namespace MapleSyrupGroup\Search\Services\Merchants\FindByUrl;

use InvalidArgumentException;
use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;
use MapleSyrupGroup\Search\Exceptions\SearchException;

/**
 * Class InvalidUrlException.
 *
 * PHP parse_url function does not produce a valid host / domain
 */
class InvalidUrlException extends InvalidArgumentException implements SearchException
{
    protected $code = ExceptionCodes::CODE_INVALID_URL;
}
