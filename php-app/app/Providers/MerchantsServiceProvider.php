<?php

namespace MapleSyrupGroup\Search\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MapleSyrupGroup\Search\Services\Client\ElasticaSearchClient;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\DomainIndexNameFactory;
use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchMerchants;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Merchants;

class MerchantsServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            Merchants::class,
            function (Application $app) {
                return new ElasticsearchMerchants(
                    $app->make(ElasticaSearchClient::class),
                    new DomainIndexNameFactory(env('ELASTICSEARCH_INDEX_NAME'))
                );
            }
        );
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            Merchants::class,
        ];
    }
}
