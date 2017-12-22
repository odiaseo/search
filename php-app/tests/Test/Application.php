<?php

namespace MapleSyrupGroup\Search\Test;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application as LaravelApplication;

trait Application
{
    /**
     * @var LaravelApplication
     */
    protected $app;

    /**
     * @var array
     */
    protected $serviceTestDoubles = [];

    /**
     * @before
     */
    protected function initApplication()
    {
        if (null !== $this->app) {
            return $this->app;
        }

        $this->app = require __DIR__ . '/../../bootstrap/app.php';
        $this->app->loadEnvironmentFrom($this->getApplicationEnvironmentFileName());
        $this->app->make(Kernel::class)->bootstrap();
        $this->app->register(new TestDoubleServiceProvider($this->app, $this->getServiceTestDoubles()));
    }

    /**
     * @after
     */
    protected function destroyApplication()
    {
        if (null !== $this->app) {
            $this->app->flush();

            $this->app = null;

            // laravel changes the error handler and doesn't bring the old one back
            restore_error_handler();

            putenv('APP_ENV=');
        }
    }

    /**
     * Useful when all the tests use doubles, but one needs to call the actual service.
     */
    protected function clearTestDoubles()
    {
        $this->serviceTestDoubles = [];
        $this->destroyApplication();
        $this->initApplication();
    }

    /**
     * @return string
     */
    protected function getApplicationEnvironmentFileName()
    {
        return '.env.servicetest';
    }

    /**
     * A map of service types to their test doubles.
     *
     * @return array
     */
    protected function getServiceTestDoubles()
    {
        return $this->serviceTestDoubles;
    }
}
