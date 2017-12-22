<?php

namespace MapleSyrupGroup\Search\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MapleSyrupGroup\Search\Models\Merchants\Filters\CategoryNameFilter;
use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantFilterAggregate;
use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantNameFilter;
use MapleSyrupGroup\Search\Models\Merchants\Filters\StopWordFilter;

/**
 * Build the merchant filters.
 */
class MerchantFilterAggregateProvider extends ServiceProvider
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
        $this->app->singleton(MerchantFilterAggregate::class, function (Application $app) {
            return new MerchantFilterAggregate(
                $app->make(StopWordFilter::class),
                $app->make(CategoryNameFilter::class),
                $app->make(MerchantNameFilter::class)
            );
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
            MerchantFilterAggregate::class,
        ];
    }
}
