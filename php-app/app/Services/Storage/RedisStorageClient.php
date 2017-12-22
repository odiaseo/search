<?php

namespace MapleSyrupGroup\Search\Services\Storage;

use Predis\ClientInterface as RedisClientInterface;

/**
 * Proxies storing data to redis.
 */
class RedisStorageClient implements StorageClient
{
    /**
     * @var RedisClientInterface
     */
    private $client;

    /**
     * Redis constructor.
     *
     * @param RedisClientInterface $client
     */
    public function __construct(RedisClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->client->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $expireInSeconds)
    {
        return $this->client->setex($key, $expireInSeconds, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function expire($key, $seconds)
    {
        return $this->client->expire($key, $seconds);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        return $this->client->exists($key);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return $this->client->del([$key]);
    }
}
