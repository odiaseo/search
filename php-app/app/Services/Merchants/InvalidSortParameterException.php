<?php

namespace MapleSyrupGroup\Search\Services\Merchants;

use InvalidArgumentException;
use MapleSyrupGroup\Search\Exceptions\SearchException;

class InvalidSortParameterException extends InvalidArgumentException implements SearchException
{

}
