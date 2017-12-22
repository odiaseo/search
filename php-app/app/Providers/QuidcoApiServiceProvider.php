<?php

namespace MapleSyrupGroup\Search\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MapleSyrupGroup\Quidco\ApiClient\Client;
use MapleSyrupGroup\Quidco\ApiClient\ClientInterface;
use MapleSyrupGroup\Search\ApiClient\CachingApiClient;
use MapleSyrupGroup\Search\Cache\Adapter\FilesystemAdapter;
use MapleSyrupGroup\Search\Cache\Adapter\FilesystemJsonAdapter;

/**
 * Build the API Client for the Quidco API
 *
 * @package MapleSyrupGroup\Search\Providers
 */
class QuidcoApiServiceProvider extends ServiceProvider
{

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerClient();
        $this->registerCachingClient();
    }

    private function registerClient()
    {
        $this->app->singleton(
            Client::class,
            function (Application $app) {
                /** @noinspection PhpUndefinedMethodInspection */
                return new Client(
                    $app->make('config')->get('quidco.api.authentication.client_id'),
                    $app->make('config')->get('quidco.api.authentication.service_key'),
                    $app->make('config')->get('quidco.api.endpoint')
                );
            }
        );
        $this->app->bind(ClientInterface::class, Client::class);
    }

    private function registerCachingClient()
    {
        $fixtureEnabled = $this->app->make('config')->get('servicetest.http.fixtures_enabled');

        if ('servicetest' !== $this->app->environment() || !$fixtureEnabled) {
            return;
        }

        $this->app->singleton(
            CachingApiClient::class,
            function (Application $app) {
                $ttl     = $app->make('config')->get('servicetest.http.fixtures_lifetime');
                $path    = $app->make('config')->get('servicetest.http.fixtures_dir');
                $adapter = new FilesystemJsonAdapter('quidco_api', $ttl, $path);

                return new CachingApiClient($app->make(Client::class), $adapter, $ttl);
            }
        );

        $this->app->bind(ClientInterface::class, CachingApiClient::class);
    }
}
