<?php

namespace MapleSyrupGroup\Search\Services\Importer\Exceptions;

use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;

/**
 * Any unexpected exception in the build process for an index
 *
 * @package MapleSyrupGroup\Search\Services\Importer\Exceptions
 */
class IndexBuildException extends \Exception
{
    protected $code = ExceptionCodes::CODE_INDEX_BUILD_ERROR;
}
