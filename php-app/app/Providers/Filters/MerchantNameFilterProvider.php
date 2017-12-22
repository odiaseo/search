<?php

namespace MapleSyrupGroup\Search\Providers\Filters;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantNameFilter;

class MerchantNameFilterProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(MerchantNameFilter::class, function (Application $app) {
            return new MerchantNameFilter($app->make('config')->get('elasticsearch.replacements.merchant_name'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            MerchantNameFilter::class,
        ];
    }
}
