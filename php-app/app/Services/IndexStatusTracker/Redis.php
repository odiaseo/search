<?php

namespace MapleSyrupGroup\Search\Services\IndexStatusTracker;

use DateInterval;
use DateTime;
use MapleSyrupGroup\Search\Services\Storage\StorageClient;

/**
 * Determine the status of the index using redis.
 */
class Redis implements IndexStatusTracker
{
    use StatusIdentifierTrait;

    /**
     * Redis client.
     *
     * @var StorageClient
     */
    private $storageClient;

    /**
     * @var int
     */
    private $ttl = 1;

    /**
     * @var string
     */
    private $cacheKey;

    /**
     * Redis constructor.
     *
     * @param StorageClient $storageClient
     * @param $ttl
     */
    public function __construct(StorageClient $storageClient, $ttl)
    {
        $this->setStorageClient($storageClient);
        $this->setTtl($ttl);

        if (php_sapi_name() === 'cli') {
            $this->setSignalHandler();
        }
    }

    /**
     * Handle situations when the indexing process is interrupted by clearing the status before exit.
     *
     * SIGINT: Ctl + C
     * SIGTSTP: Ctl + Z
     * SIGTERM: Shut down task
     */
    private function setSignalHandler()
    {
        declare (ticks = 1);

        $handler = function () {
            $this->storageClient->delete($this->cacheKey);
            posix_kill(posix_getpid(), SIGKILL);
        };

        pcntl_signal(SIGINT, $handler);
        pcntl_signal(SIGTERM, $handler);
        pcntl_signal(SIGTSTP, $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function isRunning($domainId, $statusId = null)
    {
        $cacheKey = $this->getCacheKey($domainId, $statusId);
        $client   = $this->getStorageClient();

        if (!$client->exists($cacheKey)) {
            return false;
        }

        $data = json_decode($client->get($cacheKey), true);

        if (time() - (new DateTime($data['createdAt']))->getTimestamp() > $this->getTtl()) {
            $this->unlock($domainId);

            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus($domainId, $statusId = null)
    {
        $cacheKey = $this->getCacheKey($domainId, $statusId);
        $client   = $this->getStorageClient();

        if (!$client->exists($cacheKey)) {
            return new StatusData();
        }

        $data     = json_decode($client->get($cacheKey), true);
        $expireAt = (new DateTime($data['createdAt']))->add(new DateInterval(sprintf('PT%dS', (int) $this->getTtl())));
        $data     = array_merge(
            $data,
            [
                'storage' => 'redis',
                'hint'    => sprintf('Import status would expire at %s', $expireAt->format('c')),
            ]
        );

        return new StatusData($data['uniqueId'], $data['status'], $data['createdAt'], $data['storage'], $data['hint']);
    }

    /**
     * Lock importer.
     *
     * @param int   $domainId
     * @param array $data
     * @param null  $statusId
     *
     * @return bool
     */
    public function lock($domainId, array $data, $statusId = null)
    {
        $data = array_merge(
            $data,
            [
                'status'    => 'Index running for domain ' . $domainId,
                'createdAt' => (new DateTime())->format('c'),
                'uniqueId'  => $statusId,
            ]
        );

        $key = $this->getCacheKey($domainId, $statusId);

        return (bool) $this->getStorageClient()->set($key, json_encode($data), $this->ttl);
    }

    /**
     * Release lock.
     *
     * @param int  $domainId
     * @param null $statusId
     *
     * @return bool
     */
    public function unlock($domainId, $statusId = null)
    {
        return (bool) $this->getStorageClient()->delete($this->getCacheKey($domainId, $statusId));
    }

    /**
     * @param int   $domainId
     * @param mixed $statusId
     *
     * @return string
     */
    private function getCacheKey($domainId, $statusId)
    {
        $identifier     = trim($domainId . '-' . (string) $statusId, '-');
        $this->cacheKey = sprintf('index_%s', $identifier);

        return $this->cacheKey;
    }

    /**
     * @return StorageClient
     */
    public function getStorageClient()
    {
        return $this->storageClient;
    }

    /**
     * @param StorageClient $storageClient
     *
     * @return Redis
     */
    private function setStorageClient($storageClient)
    {
        $this->storageClient = $storageClient;

        return $this;
    }

    /**
     * @return int
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * @param int $ttl
     *
     * @return Redis
     */
    private function setTtl($ttl)
    {
        if (!empty($ttl)) {
            $this->ttl = (int) $ttl;
        }

        return $this;
    }
}
