<?php
/**
 * NewRelicAttributeAddingProvider.php
 * Definition of class NewRelicAttributeAddingProvider.
 *
 * Created 30-Sep-2016, 1:35:08 PM
 * 
 * @author M.D.Ward <md.ward@quidco.com>
 * Copyright (c) 2016, Maple Syrup Media Ltd
 */

namespace MapleSyrupGroup\Search\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MapleSyrupGroup\QCommon\External\NewRelic\NewRelicAttributeAdder;
use MapleSyrupGroup\Search\Events\SearchResultsReturnedEvent;
use MapleSyrupGroup\Search\Listeners\NewRelicAttributeAddingListener;

/**
 * NewRelicAttributeAddingProvider.
 *
 * @author M.D.Ward <md.ward@quidco.com>
 */
class NewRelicAttributeAddingProvider extends ServiceProvider
{
    /**
     * Defers loading of the NewRelicAttributeAddingListener.
     * 
     * @var bool
     */
    protected $defer = true;

    /**
     * Boots the provider, binding the SearchResultsReturnedEvent to the
     * appropriate listener.
     */
    public function boot()
    {
        $app        = $this->app;
        $dispatcher = $app->make('events');

        $dispatcher->listen(
            SearchResultsReturnedEvent::class,
            function (SearchResultsReturnedEvent $event) use ($app) {
                $app
                    ->make(NewRelicAttributeAddingListener::class)
                    ->onSearchResultsReturned($event)
                ;
            }
        );
    }

    /**
     * Registers services with the container
     * Registers all services.
     */
    public function register()
    {
        $this->registerNewRelicAttributeAddingListener();
    }

    /**
     * Registers New Relic attribute adding event listener.
     *
     * @return $this
     */
    protected function registerNewRelicAttributeAddingListener()
    {
        // Failsafe condition; check that the adder service is defined first
        // @todo a better implementation of this is needed;
        // preferably not coupled to q-common
        if (!$this->app->bound(NewRelicAttributeAdder::class)) {
            $this->registerNewRelicFailsafeAdder();
        }

        $this->app->singleton(
            NewRelicAttributeAddingListener::class,
            function (Application $app) {
                return new NewRelicAttributeAddingListener(
                    $app->make(NewRelicAttributeAdder::class),
                    $app->make('log')
                );
            }
        );

        return $this;
    }

    /**
     * Registers a failsafe adder - due to the *little* fact that the q-common
     * provider does not register or boot the adder service if certain conditions
     * (i.e. environment and presence of the target callable) do not exist.
     * 
     * This method will register an instance in the container using a closure
     * which will simply log an "info" level message via the log service
     */
    protected function registerNewRelicFailsafeAdder()
    {
        $this->app->singleton(
            NewRelicAttributeAdder::class,
            function (Application $app) {
                $logger = $app->make('log');

                return new NewRelicAttributeAdder(
                    function () use ($logger) {
                        $logger->info(
                            'A request to send New Relic Attributes was received, '
                                . 'but will not be processed as this is not a production instance '
                                . 'or the New Relic extension is not loaded.'
                        );
                    }
                );
            }
        );
    }

    /**
     * Returns an array of service-registered classes provided here.
     *
     * @return array
     */
    public function provides()
    {
        return [
            NewRelicAttributeAddingListener::class,
        ];
    }
}
