<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Stubs;

use MapleSyrupGroup\Search\Exceptions\SearchException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SearchFailedException extends HttpException implements SearchException
{
}
