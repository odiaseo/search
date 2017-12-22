<?php

namespace MapleSyrupGroup\Search\Providers\Filters;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MapleSyrupGroup\Search\Models\Merchants\Filters\CategoryNameFilter;

class CategoryNameFilterProvider extends ServiceProvider
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
        $this->app->singleton(CategoryNameFilter::class, function (Application $app) {
            return new CategoryNameFilter($app->make('config')->get('elasticsearch.replacements.category_name'));
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
            CategoryNameFilter::class,
        ];
    }
}
