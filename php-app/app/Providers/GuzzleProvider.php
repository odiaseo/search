<?php

namespace MapleSyrupGroup\Search\Providers;

use GuzzleHttp\HandlerStack;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\Psr6CacheStorage;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use MapleSyrupGroup\QCommon\Guzzle;
use MapleSyrupGroup\Search\Cache\Adapter\FilesystemAdapter;

final class GuzzleProvider extends ServiceProvider
{
    const DEFAULT_CONNECTION_TIMEOUT = 15;

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->app->singleton(Guzzle::class, function (Application $app) {
            $options = ['connect_timeout' => self::DEFAULT_CONNECTION_TIMEOUT];

            if ('servicetest' === $app->environment()
                && $app->make('config')->get('servicetest.http.fixtures_enabled')
            ) {
                $stack = HandlerStack::create();
                $stack->push($this->getCacheMiddleware($app), 'cache');
                $options['handler'] = $stack;
            }

            return new Guzzle($options);
        });
    }

    /**
     * @param Application $app
     *
     * @return CacheMiddleware
     */
    private function getCacheMiddleware(Application $app)
    {
        $ttl  = $app->make('config')->get('servicetest.http.fixtures_lifetime');
        $path = $app->make('config')->get('servicetest.http.fixtures_dir');

        return new CacheMiddleware(
            new GreedyCacheStrategy(
                new Psr6CacheStorage(
                    new FilesystemAdapter('content_api', $ttl, $path)
                ),
                $ttl
            )
        );
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            Guzzle::class,
        ];
    }
}
