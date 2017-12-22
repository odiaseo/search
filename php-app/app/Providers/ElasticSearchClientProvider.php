<?php

namespace MapleSyrupGroup\Search\Providers;

use Elastica\Connection;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MapleSyrupGroup\Search\Services\Client\ElasticaSearchClient;
use Psr\Log\LoggerInterface;

/**
 * Configures and builds the elastica client.
 */
class ElasticSearchClientProvider extends ServiceProvider
{
    const KEY_CLUSTER_NODES = 'cluster_nodes';

    /**
     * Defers loading of the lastica client.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register elastica client provider.
     */
    public function register()
    {
        $this->app->singleton(ElasticaSearchClient::class, function (Application $app) {
            /* @noinspection PhpUndefinedMethodInspection */
            $config = $app->make('config')->get('elasticsearch');
            $logger = $app->make(LoggerInterface::class);
            $client = new ElasticaSearchClient($this->getConfiguredServers($config));
            $client->setLogger($logger);

            return $client;
        });
    }

    /**
     * Add configured cluster nodes to connection pool
     * If in cli mode, use only the master configuration - assumption is that index creation is done via the console.
     *
     * @param array $config
     *
     * @return array
     */
    private function getConfiguredServers(array $config)
    {
        if (PHP_SAPI !== 'cli' && !empty($config[self::KEY_CLUSTER_NODES])) {
            $clusterNodes = $this->processClusterNodeParameters($config[self::KEY_CLUSTER_NODES]);

            $config['client']['servers'] = array_merge(
                $config['client']['servers'],
                $clusterNodes
            );
        }

        return $config['client'];
    }

    /**
     * Process cluster node server parameters from config.
     * Use default port if not set.
     *
     * @param string $configString
     *
     * @return array
     */
    public function processClusterNodeParameters($configString)
    {
        $params = [];
        $hosts  = array_filter(explode(',', $configString));

        foreach ($hosts as $host) {
            list($ipAddress, $port) = explode(':', $host . ':');

            $params[] = [
                'host' => (string) $ipAddress,
                'port' => $port ? (int) $port : Connection::DEFAULT_PORT,
            ];
        }

        return $params;
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            ElasticaSearchClient::class,
        ];
    }
}
