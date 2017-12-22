<?php

namespace MapleSyrupGroup\Search\Providers;

use MapleSyrupGroup\Annotations\Providers\ValidatorProvider;
use MapleSyrupGroup\QCommon\Validators\ApiValidator;
use MapleSyrupGroup\Search\Http\Request\ApiRequest;

class ApiValidatorProvider extends ValidatorProvider
{
    protected $requestClass   = ApiRequest::class;
    protected $validatorClass = ApiValidator::class;
}
