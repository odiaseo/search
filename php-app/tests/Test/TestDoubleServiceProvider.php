<?php

namespace MapleSyrupGroup\Search\Test;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;

/**
 * Makes it possible to replace services with test doubles.
 */
class TestDoubleServiceProvider extends ServiceProvider
{
    /**
     * A map of service types to their test doubles.
     *
     * @var array
     */
    private $services = [];

    protected $defer = true;

    /**
     * @param LaravelApplication $app
     * @param array $services
     */
    public function __construct(LaravelApplication $app, array $services = [])
    {
        parent::__construct($app);

        $this->services = $services;
    }

    /**
     * Replaces configured services with test doubles.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->services as $class => $testDouble) {
            $this->app->singleton($class, function () use ($testDouble) {
                return $testDouble;
            });
        }
    }

    public function provides()
    {
        return array_keys($this->services);
    }


}