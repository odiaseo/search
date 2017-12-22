<?php

namespace MapleSyrupGroup\Search\Services\IndexStatusTracker;

use Illuminate\Config\Repository;

/**
 * @group tracker
 */
class TrackerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider trackerDataProvider
     *
     * @param string $alias
     * @param string $className
     */
    public function testFactoryCanCreateStatusTrackerByAlias($alias, $className)
    {
        $config = new Repository(
            [
                'importer' => [
                    'lock_file_location' => '/tmp',
                ],
                'redis'    => [
                    'scheme' => 'redis',
                ],
            ]
        );

        $factory = new TrackerFactory($config);
        $tracker = $factory->createTracker($alias);

        $this->assertInstanceOf($className, $tracker);
    }

    public function trackerDataProvider()
    {
        return [
            [TrackerFactory::REDIS, Redis::class],
            [TrackerFactory::LOCK_FILE, Lockfile::class],
            ['default', Lockfile::class],
        ];
    }
}
