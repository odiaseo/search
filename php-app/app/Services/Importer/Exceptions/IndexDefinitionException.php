<?php

namespace MapleSyrupGroup\Search\Services\Importer\Exceptions;

use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;

/**
 * Could not define the index as configured
 *
 * @package MapleSyrupGroup\Search\Services\Importer\Exceptions
 */
class IndexDefinitionException extends \DomainException
{
    protected $code = ExceptionCodes::CODE_CAN_NOT_DEFINE_INDEX;
}
