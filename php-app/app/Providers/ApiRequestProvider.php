<?php

namespace MapleSyrupGroup\Search\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MapleSyrupGroup\QCommon\Http\Requests\RequestParams;
use MapleSyrupGroup\Search\Http\Request\ApiRequest;
use MapleSyrupGroup\Search\Http\Request\Request;

class ApiRequestProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->bind(ApiRequest::class, Request::class);

        $this->app->resolving(function (RequestParams $requestParams, Application $app) {
            $requestParams->initFromRequest($app->make(ApiRequest::class));

            return $requestParams;
        });
    }

    public function provides()
    {
        return [
            ApiRequest::class,
        ];
    }
}
