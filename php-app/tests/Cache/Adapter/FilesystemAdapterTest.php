<?php

namespace MapleSyrupGroup\Search\Cache\Adapter;

use Cache\IntegrationTests\CachePoolTest;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\Cache\Tests\Adapter\FilesystemAdapterTest as BaseFilesystemAdapterTest;

/**
 * @group integration
 */
class FilesystemAdapterTest extends BaseFilesystemAdapterTest
{
    protected function setUp()
    {
        parent::setUp();

        // this is needed until php-cache/integration-tests merges the clock mocking pull request
        ClockMock::register(CachePoolTest::class);
        ClockMock::register(FilesystemAdapter::class);
        ClockMock::withClockMock(true);
    }

    public function testDefaultLifeTime()
    {
        if (isset($this->skippedTests[__FUNCTION__])) {
            $this->markTestSkipped($this->skippedTests[__FUNCTION__]);
        }

        $cache = $this->createCachePool(2);

        $item = $cache->getItem('key.dlt');
        $item->set('value');
        $cache->save($item);
        sleep(1);

        $item = $cache->getItem('key.dlt');
        $this->assertTrue($item->isHit());
    }

    protected function tearDown()
    {
        parent::tearDown();

        ClockMock::withClockMock(false);
    }


    public function createCachePool()
    {
        return new FilesystemAdapter('sf-cache');
    }
}
