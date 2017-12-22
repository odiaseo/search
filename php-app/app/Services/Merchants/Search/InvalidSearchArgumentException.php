<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Search;

use InvalidArgumentException;
use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;
use MapleSyrupGroup\Search\Exceptions\SearchException;

class InvalidSearchArgumentException extends InvalidArgumentException implements SearchException
{
    protected $code = ExceptionCodes::CODE_INVALID_ARGUMENT;
}
