<?php

namespace MapleSyrupGroup\Search\Services\Storage;

use Predis\Client as PredisClient;
use Predis\Response\Status;

/**
 * @group tracker
 * @group integration
 */
class RedisStorageClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RedisStorageClient
     */
    private $client;

    /**
     * @var string
     */
    private $testData;

    /**
     * @var string
     */
    private $key = 'test-key';

    /**
     * @var int
     */
    private $ttl;

    /**
     * @var int
     */
    private $database = 4;

    public function setUp()
    {
        $pRedisClient = new PredisClient(
            [
                'scheme'   => 'tcp',
                'host'     => getenv('REDIS_HOST') !== false ? getenv('REDIS_HOST') : '127.0.0.1',
                'port'     => getenv('REDIS_PORT') !== false ? getenv('REDIS_PORT') : 6379,
                'database' => getenv('REDIS_DATABASE') !== false ? getenv('REDIS_DATABASE') : $this->database
            ],
            [
                'prefix' => 'IndexStatusTracker:',
            ]
        );

        $this->client   = new RedisStorageClient($pRedisClient);
        $this->testData = json_encode(
            [
                'started_at' => '2016-08-08T14:27:13+00:00',
                'status_id'  => null,
                'status'     => 'Index running for domain 1',
            ]
        );
        $this->ttl      = 5;
    }

    public function testClientCanStoreDataToRedis()
    {
        $return = $this->client->set($this->key, $this->testData, $this->ttl);
        $this->assertInstanceOf(Status::class, $return);
        $this->assertSame('OK', (string) $return);
    }

    public function testClientCanRetrieveDataStoredInRedis()
    {
        $this->client->set($this->key, $this->testData, $this->ttl);
        $return = $this->client->get($this->key);
        $this->assertSame($this->testData, $return);
    }

    public function testClientCanDetermineAnEntryExistsInRedis()
    {
        $this->client->set($this->key, $this->testData, $this->ttl);
        $return = $this->client->exists($this->key);
        $this->assertInternalType('integer', $return);
        $this->assertSame(1, $return);
    }

    public function testClientCanSetTimeToExpireInRedis()
    {
        $this->client->set($this->key, $this->testData, $this->ttl);
        $return = $this->client->expire($this->key, $this->ttl);
        $this->assertInternalType('integer', $return);
        $this->assertSame(1, $return);
    }

    public function testDataCanBeDeletedFromRedis()
    {
        $this->client->set($this->key, $this->testData, $this->ttl);
        $return = $this->client->delete($this->key);
        $this->assertInternalType('integer', $return);
        $this->assertSame(1, $return);
    }

    /**
     * @depends testDataCanBeDeletedFromRedis
     */
    public function testClientCanDetermineAnEntryDoesNotExistsInRedis()
    {
        $return = $this->client->exists($this->key);
        $this->assertInternalType('integer', $return);
        $this->assertSame(0, $return);
    }

    public function tearDown()
    {
        $this->client->delete($this->key);
    }
}
