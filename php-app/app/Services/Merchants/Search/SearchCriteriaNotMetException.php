<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Search;

use InvalidArgumentException;
use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;
use MapleSyrupGroup\Search\Exceptions\SearchException;

class SearchCriteriaNotMetException extends InvalidArgumentException implements SearchException
{
    protected $code = ExceptionCodes::CODE_SEARCH_CRITERIA_NOT_MET;
}
