<?php

namespace MapleSyrupGroup\Search\Services\Merchants\FindByUrl;

use InvalidArgumentException;
use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;
use MapleSyrupGroup\Search\Exceptions\SearchException;

class NoMerchantFoundException extends InvalidArgumentException implements SearchException
{
    protected $code = ExceptionCodes::CODE_MERCHANT_NOT_FOUND;
}
