<?php

namespace MapleSyrupGroup\Search\Behat\Search;

use Elastica\Exception\ResponseException;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application as LaravelApplication;
use MapleSyrupGroup\QCommon\Enum\DomainEnum;
use MapleSyrupGroup\Search\Services\Client\ElasticaSearchClient;
use MapleSyrupGroup\Search\Services\Importer\Import;
use Psr\Log\LoggerInterface;

/**
 * Builds the index for tests.
 *
 * It will only build the index if it hasn't already been built, unless the `alwaysRefreshIndex` flag is set to true.
 */
class TestImport
{
    /**
     * @var LaravelApplication|null
     */
    private $app;

    /**
     * @var string
     */
    private $appEnvironment;

    /**
     * @var string
     */
    private $indexPrefix;

    /**
     * @var bool
     */
    private $alwaysRefreshIndex;

    /**
     * @var string
     */
    private $appPath;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param string $appPath
     * @param string $appEnvironment
     * @param string $indexPrefix
     * @param bool $alwaysRefreshIndex
     * @param LoggerInterface $logger
     */
    public function __construct(
        $appPath,
        $appEnvironment,
        $indexPrefix,
        $alwaysRefreshIndex = false,
        LoggerInterface $logger = null
    ) {
        if (!file_exists($appPath)) {
            throw new \InvalidArgumentException(sprintf('The "%s" application file does not exist.', $appPath));
        }

        $this->appEnvironment     = $appEnvironment;
        $this->indexPrefix        = $indexPrefix;
        $this->alwaysRefreshIndex = $alwaysRefreshIndex;
        $this->appPath            = $appPath;
        $this->logger             = $logger;
    }

    public function import()
    {
        // we want to postpone the initialization and avoid it if the import wasn't called
        $this->initializeApplication();

        if ($this->alwaysRefreshIndex) {
            $this->deleteIndices();
        }

        if (!$this->indicesExist()) {
            $import = $this->getImport();
            $import->doImport($this->indexPrefix, null, $this->logger);
        }

        $this->destroyApplication();
    }

    private function initializeApplication()
    {
        if (null !== $this->app) {
            return;
        }

        putenv(sprintf('APP_ENV=%s', $this->appEnvironment));

        $this->app = require $this->appPath;
        $this->app->make(Kernel::class)->bootstrap();
    }

    /**
     * @return LaravelApplication
     */
    private function getApplication()
    {
        if (null === $this->app) {
            throw new \LogicException('The application was never initialized.');
        }

        return $this->app;
    }

    private function destroyApplication()
    {
        $this->app->flush();

        $this->app = null;

        // laravel changes the error handler and doesn't bring the old one back
        restore_error_handler();

        putenv('APP_ENV=');
    }

    private function deleteIndices()
    {
        $indices = [
            $this->indexPrefix . '_' . DomainEnum::DOMAIN_ID_SHOOP,
            $this->indexPrefix . '_' . DomainEnum::DOMAIN_ID_QUIDCO,
        ];

        foreach ($indices as $index) {
            $this->deleteIndex($index);
        }
    }

    /**
     * @param string $index
     */
    private function deleteIndex($index)
    {
        try {
            $this->getElastica()->getIndex($index)->delete();
        } catch (ResponseException $e) {
            // index might not exist
        }
    }

    /**
     * @return bool
     */
    private function indicesExist()
    {
        $response = $this->getElastica()->request('_cat/indices', 'GET')->getData();

        return isset($response['message']) && preg_match(
            '#' . preg_quote($this->indexPrefix) . '#',
            $response['message']
        );
    }

    /**
     * @return Import
     */
    private function getImport()
    {
        return $this->getApplication()->make(Import::class);
    }

    /**
     * @return ElasticaSearchClient
     */
    private function getElastica()
    {
        return $this->getApplication()->make(ElasticaSearchClient::class);
    }
}
