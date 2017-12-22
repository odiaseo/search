<?php

namespace MapleSyrupGroup\Search\Providers;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MapleSyrupGroup\Search\Logs\LogFormatter;
use MapleSyrupGroup\Search\Logs\Rfc5424Formatter;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLoggerImplementation;
use Monolog\Handler\SocketHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Build the merchant search provider service.
 */
class BusinessEventLoggerProvider extends ServiceProvider
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
        $this->app->singleton(BusinessEventLoggerImplementation::class, function (Application $app) {
            /** @var Config $config */
            $config     = $app->make('config');
            $socket     = $config->get('papertrail.syslog.socket', false);
            $systemName = $config->get('papertrail.syslog.system-name', false);

            if (in_array(false, [$systemName, $socket], true) === true) {
                /** @var LoggerInterface $logger */
                $logger = $app->make(LoggerInterface::class);
            } else {
                $logger   = new Logger($systemName);
                $logLevel = $config->get('app.log_level');

                /** @var callable $processor */
                foreach ($app->make('monolog-processors') as $processor) {
                    $logger->pushProcessor($processor);
                }

                // Setup the logger
                $syslogHandler = new SocketHandler($socket, $logLevel);

                // Set the format
                $formatter = new Rfc5424Formatter(new LogFormatter());
                $syslogHandler->setFormatter($formatter);

                $logger->pushHandler($syslogHandler);
            }

            return new BusinessEventLoggerImplementation($logger);
        });
        $this->app->bind(BusinessEventLogger::class, BusinessEventLoggerImplementation::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            BusinessEventLogger::class,
            BusinessEventLoggerImplementation::class,
        ];
    }
}
