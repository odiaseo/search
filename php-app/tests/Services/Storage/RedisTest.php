<?php

namespace MapleSyrupGroup\Search\Services\Storage;

use DateTime;
use MapleSyrupGroup\Search\Services\IndexStatusTracker\Redis;
use MapleSyrupGroup\Search\Services\IndexStatusTracker\StatusData;
use Prophecy\Argument;

/**
 * @group tracker
 */
class RedisTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $key = 'index_1';

    /**
     * @var int
     */
    private $ttl = 5;

    /**
     * @var int
     */
    private $domainId = 1;

    /**
     * @var int
     */
    private $statusId = 2;

    private $sampleStatus;

    public function setUp()
    {
        $this->sampleStatus = [
            'createdAt' => (new DateTime())->format('c'),
            'uniqueId'  => 'a67cbe92',
            'status'    => 'running',
            'hint'      => '',
            'storage'   => 'redis',
        ];
    }

    public function testRedisTrackerReturnsFalseWhenIndexIsNotRunning()
    {
        $client = $this->prophesize(RedisStorageClient::class);
        $client->exists($this->key)->willReturn(false);
        $tracker = new Redis($client->reveal(), $this->ttl);

        $this->assertFalse($tracker->isRunning($this->domainId));
    }

    public function testRedisTrackerReturnsFalseWhenIndexIsNotRunningWithExpiredContent()
    {
        $client = $this->prophesize(RedisStorageClient::class);
        $client->exists($this->key)->willReturn(true);
        $client->delete($this->key)->shouldBeCalled();
        $client->get($this->key)->willReturn(json_encode(['createdAt' => (new DateTime('-1 day'))->format('c')]));

        $tracker = new Redis($client->reveal(), $this->ttl);

        $this->assertFalse($tracker->isRunning($this->domainId));
    }

    public function testRedisTrackerReturnsTrueWhenIndexIsRunning()
    {
        $client = $this->prophesize(RedisStorageClient::class);
        $client->get($this->key)->shouldBeCalled();
        $client->exists($this->key)->willReturn(true);

        $tracker = new Redis($client->reveal(), $this->ttl);

        $this->assertTrue($tracker->isRunning($this->domainId));
    }

    public function testRedisTrackerCanStoreStatusData()
    {
        $client = $this->prophesize(RedisStorageClient::class);
        $client->set($this->key, Argument::any(), $this->ttl)->willReturn(1);
        $tracker = new Redis($client->reveal(), $this->ttl);

        $result = $tracker->lock($this->domainId, []);
        $this->assertTrue($result);
    }

    public function testRedisTrackerCanRetrieveStatusData()
    {
        $client = $this->prophesize(RedisStorageClient::class);
        $client->get($this->key)->willReturn(json_encode($this->sampleStatus));
        $client->exists($this->key)->willReturn(true);

        $tracker = new Redis($client->reveal(), $this->ttl);
        $array   = $tracker->getStatus($this->domainId)->toArray();

        $this->assertInternalType('array', $array);
        $this->assertArrayHasKey('storage', $array);
        $this->assertSame('redis', $array['storage']);
    }

    public function testRedisTrackerReturnsEmptyStringWhenNoStatusExists()
    {
        $client = $this->prophesize(RedisStorageClient::class);
        $client->exists($this->key)->willReturn(false);

        $tracker = new Redis($client->reveal(), $this->ttl);
        $return  = $tracker->getStatus($this->domainId);

        $this->assertInstanceOf(StatusData::class, $return);
        $this->assertSame(StatusData::DEFAULT_STATUS, $return->toArray()['status']);
    }

    public function testRedisTrackerCanUnlockIndexStatus()
    {
        $client = $this->prophesize(RedisStorageClient::class);
        $client->delete($this->key . '-2')->willReturn(1);
        $tracker = new Redis($client->reveal(), $this->ttl);

        $result = $tracker->unlock($this->domainId, $this->statusId);
        $this->assertTrue($result);
    }

    public function testThatStatusDataCanBeConvertedToString()
    {
        $client = $this->prophesize(RedisStorageClient::class);
        $client->get($this->key)->willReturn(json_encode($this->sampleStatus));
        $client->exists($this->key)->willReturn(true);

        $tracker = new Redis($client->reveal(), $this->ttl);
        $array   = $tracker->getStatus($this->domainId);

        $this->assertInternalType('string', (string) $array);
    }

    public function testIndexTrackerCanGenerateValidIdentifiers()
    {
        $client = $this->prophesize(RedisStorageClient::class);
        $client->exists($this->key)->willReturn(true);

        $tracker    = new Redis($client->reveal(), $this->ttl);
        $identifier = $tracker->getUniqueIdentifier();

        $this->assertInternalType('string', $identifier);
        $this->assertSame($tracker->getIdLength(), strlen($identifier));
    }
}
