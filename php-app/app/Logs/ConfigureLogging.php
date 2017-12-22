<?php

namespace MapleSyrupGroup\Search\Logs;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\ConfigureLogging as BaseConfigureLogging;
use Illuminate\Log\Writer;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Processor\ProcessIdProcessor;
use Psr\Log\LogLevel;

/**
 * Configure the logging generally.
 *
 * Adds process ID to all logs
 *
 * @package MapleSyrupGroup\Search\Logs
 */
class ConfigureLogging extends BaseConfigureLogging
{
    /**
     * Index to log into
     */
    const ELASTICSEARCH_INDEX = 'application';

    /**
     * Type to log into
     */
    const ELASTICSEARCH_TYPE = 'group-api';

    /**
     * @var string
     */
    private $logLevel;

    /**
     * @var int
     */
    private $fingersCrossedBufferSize = 30;

    /**
     * ConfigureLogging constructor.
     */
    public function __construct()
    {
        $this->logLevel = env('LOG_LEVEL', LogLevel::ERROR);
        $this->fingersCrossedBufferSize = (int) env('LOG_FINGERS_CROSSED_BUFFER_SIZE', 30);
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  Application $app
     * @param  Writer      $log
     *
     * @return void
     */
    protected function configureHandlers(Application $app, Writer $log)
    {
        $monologProcessors = $this->getMonologProcessors();
        $app->bind('monolog-processors', function () use ($monologProcessors) {
            return $monologProcessors;
        });

        parent::configureHandlers($app, $log);

        $this->configureFingersCrossedHandler($app, $log);

        $monolog = $log->getMonolog();

        foreach ($app->make('monolog-processors') as $monologProcessor) {
            $monolog->pushProcessor($monologProcessor);
        }
    }


    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Log\Writer                       $log
     *
     * @return void
     */
    protected function configureSingleHandler(Application $app, Writer $log)
    {
        $log->useFiles($app->storagePath() . '/logs/laravel.log', $this->logLevel);
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Log\Writer                       $log
     *
     * @return void
     */
    protected function configureDailyHandler(Application $app, Writer $log)
    {
        $log->useDailyFiles(
            $app->storagePath() . '/logs/laravel.log',
            $app->make('config')->get('app.log_max_files', 5),
            $this->logLevel
        );
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Log\Writer                       $log
     *
     * @return void
     */
    protected function configureSyslogHandler(Application $app, Writer $log)
    {
        $log->useSyslog('laravel', $this->logLevel);
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Illuminate\Log\Writer                       $log
     *
     * @return void
     */
    protected function configureErrorlogHandler(Application $app, Writer $log)
    {
        $log->useErrorLog($this->logLevel);
    }

    /**
     * Returns an array of callable objects that will be used as monolog processors
     *
     * @return ProcessIdProcessor[]
     */
    protected function getMonologProcessors()
    {
        return [new ProcessIdProcessor()];
    }

    /**
     * Decorates all registered handlers with the fingers crossed handler (unless in debug mode).
     *
     * @param Application $app
     * @param Writer      $log
     */
    private function configureFingersCrossedHandler(Application $app, Writer $log)
    {
        if ($app->make('config')->get('app.debug')) {
            return;
        }

        $handlers = [];

        foreach ($log->getMonolog()->getHandlers() as $handler) {
            $handlers[] = new FingersCrossedHandler($handler, null, $this->fingersCrossedBufferSize, true, false);
        }

        $log->getMonolog()->setHandlers($handlers);
    }
}
