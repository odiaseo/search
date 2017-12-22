<?php

namespace MapleSyrupGroup\Search\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MapleSyrupGroup\Search\Services\IndexStatusTracker\IndexStatusTracker;
use MapleSyrupGroup\Search\Services\IndexStatusTracker\TrackerFactory;

/**
 * Configures and builds index status tracker based on env configuration
 */
class IndexStatusTrackerProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * Registers index status tracker
     */
    public function register()
    {
        $this->app->singleton(IndexStatusTracker::class, function (Application $app) {
            $config        = $app->make('config');
            $alias         = $config->get('importer.tracker_alias');
            $statusTracker = (new TrackerFactory($config))->createTracker($alias);

            return $statusTracker;
        });
    }

    /**
     * Service provided
     *
     * @return array
     */
    public function provides()
    {
        return [
            IndexStatusTracker::class,
        ];
    }
}
