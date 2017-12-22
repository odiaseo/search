<?php

namespace MapleSyrupGroup\Search\Foundation\Bootstrap;

use Dotenv;
use Illuminate\Foundation\Application;
use InvalidArgumentException;

/**
 * Detect if we're in a Behat test environment or not.
 */
class ConsoleDetectEnvironment
{
    const SERVICE_TEST_KEY = 'servicetest';
    const APP_ENV_KEY      = 'APP_ENV';

    /**
     * Bootstrap the given application.
     *
     * @param Application $app
     *
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function bootstrap(Application $app)
    {
        try {
            $environmentFile = $this->getEnvironmentFilename($app->environmentFile(), $this->isAppInService());
            Dotenv::load($app->environmentPath(), $environmentFile);
        } catch (InvalidArgumentException $e) {
            //
        }

        $app->detectEnvironment(function () {
            return env('APP_ENV', 'production');
        });
    }

    /**
     * Switch env file if in test mode.
     *
     * @param $environmentFile
     * @param $appEnvIsService
     *
     * @return string
     */
    public function getEnvironmentFilename($environmentFile, $appEnvIsService)
    {
        if ($this->isHttpTestMode() || $appEnvIsService) {
            return $environmentFile . '.' . self::SERVICE_TEST_KEY;
        }

        return $environmentFile;
    }

    /**
     * Determine if the a service test environment.
     *
     * @return bool
     */
    private function isAppInService()
    {
        return getenv(self::APP_ENV_KEY) !== false && getenv(self::APP_ENV_KEY) === self::SERVICE_TEST_KEY;
    }

    /**
     * Determine if the API request is in test mode
     * Used when running behat acceptance tests.
     *
     * @return bool
     */
    protected function isHttpTestMode()
    {
        return false;
    }
}
