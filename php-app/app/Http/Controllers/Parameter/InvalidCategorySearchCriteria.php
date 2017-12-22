<?php

namespace MapleSyrupGroup\Search\Http\Controllers\Parameter;

use Behat\Behat\Definition\Exception\SearchException;
use InvalidArgumentException;

class InvalidCategorySearchCriteria extends InvalidArgumentException implements SearchException
{
}
