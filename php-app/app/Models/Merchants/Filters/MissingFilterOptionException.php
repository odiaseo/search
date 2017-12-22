<?php

namespace MapleSyrupGroup\Search\Models\Merchants\Filters;

use InvalidArgumentException;
use MapleSyrupGroup\Search\Exceptions\SearchException;

class MissingFilterOptionException extends InvalidArgumentException implements SearchException
{
}
