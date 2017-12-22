<?php

namespace MapleSyrupGroup\Search\Services\IndexStatusTracker;

use Illuminate\Contracts\Config\Repository;
use MapleSyrupGroup\Search\Services\Storage\RedisStorageClient;
use Predis\Client as PredisClient;

/**
 * Create tracker instance.
 */
class TrackerFactory
{
    const REDIS = 'redis';
    const LOCK_FILE = 'lockfile';

    /**
     * @var Repository
     */
    private $config;

    /**
     * TrackerFactory constructor.
     *
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * @param $alias
     *
     * @return IndexStatusTracker
     */
    public function createTracker($alias)
    {
        switch (strtolower($alias)) {
            case self::REDIS:
                return $this->getRedisTracker();

            case self::LOCK_FILE:
            default:
                return $this->getLockfileTracker();
        }
    }

    /**
     * @return Redis
     */
    private function getRedisTracker()
    {
        $pRedisClient = new PredisClient(
            [
                'scheme'   => $this->config->get('redis.scheme'),
                'host'     => $this->config->get('redis.host'),
                'port'     => $this->config->get('redis.port'),
                'database' => $this->config->get('redis.database'),
            ],
            [
                'prefix' => $this->getPrefix(),
            ]
        );

        return new Redis(new RedisStorageClient($pRedisClient), $this->config->get('importer.lock_file_ttl'));
    }

    /**
     * @return string
     */
    private function getPrefix()
    {
        return trim(strtolower($this->config->get('redis.prefix') . 'IndexStatusTracker:'));
    }

    /**
     * @return Lockfile
     */
    private function getLockfileTracker()
    {
        $location    = $this->config->get('importer.lock_file_location');
        $lockFileTtl = $this->config->get('importer.lock_file_ttl');

        return new Lockfile($location, $lockFileTtl);
    }
}
