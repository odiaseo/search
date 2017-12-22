<?php


namespace MapleSyrupGroup\Search\Http;

use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Bootstrap\RegisterFacades;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use MapleSyrupGroup\QCommon\Http\Middleware\FractalMiddleware;
use MapleSyrupGroup\QCommon\Http\Middleware\ResponseHeadersMiddleware;
use MapleSyrupGroup\Search\Foundation\Bootstrap\HttpDetectEnvironment;
use MapleSyrupGroup\Search\Logs\ConfigureWebLogging;

/**
 * Kernel for HTTP side of application
 *
 * @package MapleSyrupGroup\Search\Http
 */
class Kernel extends HttpKernel
{
    /**
     * The bootstrap classes for the application.
     *
     * @var array
     */
    protected $bootstrappers = [
        HttpDetectEnvironment::class,
        LoadConfiguration::class,
        ConfigureWebLogging::class,
        HandleExceptions::class,
        RegisterFacades::class,
        RegisterProviders::class,
        BootProviders::class,
    ];

    /**
     * The application's HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        CheckForMaintenanceMode::class,
        ResponseHeadersMiddleware::class
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'fractal' => FractalMiddleware::class,
    ];
}
