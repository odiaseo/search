<?php

namespace MapleSyrupGroup\Search\Services\IndexStatusTracker;

use org\bovigo\vfs\vfsStream;

/**
 * @group tracker
 */
class LockfileTest extends \PHPUnit_Framework_TestCase
{
    const DOMAIN    = 1;
    const STATUS_ID = '5aec782d';

    /**
     * @var Lockfile
     */
    private $tracker;

    private $root;

    private $directory;

    public function setUp()
    {
        $this->root      = vfsStream::setup('lockDirectory');
        $this->directory = vfsStream::url('lockDirectory');
        $this->tracker   = new Lockfile($this->directory, 0);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionIsThrownWithANonWritableDirectory()
    {
        new Lockfile('i-do-no-exist', 0);
    }

    public function testIndexTrackerCanRetrieveIndexStatus()
    {
        $this->tracker->unlock(self::DOMAIN, self::STATUS_ID);

        $this->assertFalse($this->tracker->isRunning(self::DOMAIN, self::STATUS_ID));
        $status = $this->tracker->getStatus(self::DOMAIN, self::STATUS_ID);
        $this->assertInstanceOf(StatusData::class, $status);

        $this->assertSame(StatusData::DEFAULT_STATUS, $status->toArray()['status']);

        $this->tracker->lock(self::DOMAIN, [], self::STATUS_ID);
        $data = $this->tracker->getStatus(self::DOMAIN, self::STATUS_ID);
        $this->assertInstanceOf(StatusData::class, $data);

        $status = $data->toArray();
        $this->assertNotEmpty($status);
        $this->assertInternalType('array', $status);

        $this->assertTrue($this->tracker->isRunning(self::DOMAIN, self::STATUS_ID));

        $this->assertArrayHasKey('id', $status);
        $this->assertArrayHasKey('storage', $status);
        $this->assertArrayHasKey('status', $status);
        $this->assertArrayHasKey('created_at', $status);
        $this->assertArrayHasKey('hint', $status);

        $this->assertSame(self::STATUS_ID, $status['id']);
    }

    public function testLockFileIsAutoDeletedAfterTtlExpires()
    {
        $tracker = new Lockfile($this->directory, 1);
        $tracker->lock(1, []);
        $this->assertTrue($tracker->isRunning(1));
        sleep(2);
        $this->assertFalse($tracker->isRunning(1));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionIsThrownWhenAnInvalidIdentifierIsProvided()
    {
        $tracker = new Lockfile($this->directory, 3);
        $tracker->getStatus(self::DOMAIN, str_repeat('*', $tracker->getIdLength() * 2));
    }

    public function testThatLockFilenameCanBeCreatedWhenNoDomainIsSpecified()
    {
        $tracker  = new Lockfile($this->directory, 1);
        $filename = $tracker->getFilename('', '');
        $this->assertNotEmpty($filename);
    }
}
