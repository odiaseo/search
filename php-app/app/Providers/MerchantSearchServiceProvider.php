<?php

namespace MapleSyrupGroup\Search\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantFilterAggregate;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger;
use MapleSyrupGroup\Search\Services\Client\ElasticaSearchClient;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\DomainIndexNameFactory;
use MapleSyrupGroup\Search\Services\Merchants\Factory\SearchFactory;
use MapleSyrupGroup\Search\Services\Merchants\Search;
use MapleSyrupGroup\Search\Services\Merchants\Search\SearchStrategyCollection;

/**
 * Build the merchant search provider service.
 */
class MerchantSearchServiceProvider extends ServiceProvider
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
        $this->app->singleton(
            SearchStrategyCollection::class,
            function (Application $app) {
                $factory = new SearchFactory(
                    $app->make(BusinessEventLogger::class),
                    $app->make(ElasticaSearchClient::class),
                    new DomainIndexNameFactory(env('ELASTICSEARCH_INDEX_NAME')),
                    $app->make(MerchantFilterAggregate::class),
                    $app->make('config')->get('elasticsearch.search_strategy_log_enabled', false)
                );

                return $factory->createSearch();
            }
        );
        $this->app->bind(Search::class, SearchStrategyCollection::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Search::class,
            SearchStrategyCollection::class,
        ];
    }
}
