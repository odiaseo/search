<?php

namespace MapleSyrupGroup\Search\Providers\Filters;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MapleSyrupGroup\Search\Models\Merchants\Filters\StopWordFilter;

class StopWordFilterProvider extends ServiceProvider
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
        $this->app->singleton(StopWordFilter::class, function (Application $app) {
            return new StopWordFilter($app->make('config')->get('elasticsearch.stopWords'));
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
            StopWordFilter::class,
        ];
    }
}
