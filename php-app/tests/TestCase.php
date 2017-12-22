<?php
namespace MapleSyrupGroup\Search;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Testing\TestCase as LaravelTestCase;
use Illuminate\Http\Request;

abstract class TestCase extends LaravelTestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        /**
         * @var \Illuminate\Foundation\Application $app
         */
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->instance('request', Request::capture());
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
