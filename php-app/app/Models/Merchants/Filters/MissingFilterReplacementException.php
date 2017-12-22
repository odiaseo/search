<?php

namespace MapleSyrupGroup\Search\Models\Merchants\Filters;

use MapleSyrupGroup\Search\Exceptions\SearchException;

class MissingFilterReplacementException extends \LogicException implements SearchException
{
}
