<?php

namespace MapleSyrupGroup\Search\ApiClient;

use MapleSyrupGroup\Quidco\ApiClient\ClientInterface;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * @group integration
 */
class CachingApiClientTest extends \PHPUnit_Framework_TestCase
{
    const CONTROLLER = 'merchant';

    const HTTP_METHOD = 'get';

    const ACTION = 'enriched';

    const PARAMS = ['page' => 13];

    const LANGUAGE = 'en';

    const FILE = 'test.csv';

    const LIFETIME = 5;

    /**
     * @var CachingApiClient
     */
    private $cachingApiClient;

    /**
     * @var ClientInterface|ObjectProphecy
     */
    private $apiClient;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    protected function setUp()
    {
        // since the array adapter is not the class under test we need to manually register its namespace
        // so the time functions used by it are mocked
        ClockMock::register(ArrayAdapter::class);

        $this->apiClient = $this->prophesize(ClientInterface::class);
        $this->cache     = new ArrayAdapter();

        $this->cachingApiClient = new CachingApiClient(
            $this->apiClient->reveal(),
            $this->cache,
            self::LIFETIME
        );
    }

    public function testItReturnsTheResponseReturnedByTheDecoratedClient()
    {
        $response = (object)['foo' => 'bar'];

        $this->apiClient->call(
            self::CONTROLLER,
            self::HTTP_METHOD,
            self::ACTION,
            self::PARAMS,
            self::LANGUAGE,
            self::FILE
        )->willReturn($response);

        $actualResponse = $this->cachingApiClient->call(
            self::CONTROLLER,
            self::HTTP_METHOD,
            self::ACTION,
            self::PARAMS,
            self::LANGUAGE,
            self::FILE
        );

        $this->assertSame($response, $actualResponse);
    }

    public function testItStoresTheResponseInCache()
    {
        $response = (object)['foo' => 'bar'];

        $this->apiClient->call(Argument::cetera())->willReturn($response);

        $this->cachingApiClient->call(
            self::CONTROLLER,
            self::HTTP_METHOD,
            self::ACTION,
            self::PARAMS,
            self::LANGUAGE,
            self::FILE
        );

        $this->assertTrue($this->cache->hasItem($this->getCacheKey()));
        $this->assertTrue($this->cache->getItem($this->getCacheKey())->isHit());
    }

    /**
     * @group time-sensitive
     */
    public function testItStoresTheResponseInCacheForTheGivenLifetime()
    {
        $response = (object)['foo' => 'bar'];

        $this->apiClient->call(Argument::cetera())->willReturn($response);

        $this->cachingApiClient->call(
            self::CONTROLLER,
            self::HTTP_METHOD,
            self::ACTION,
            self::PARAMS,
            self::LANGUAGE,
            self::FILE
        );

        sleep(self::LIFETIME + 100);

        $this->assertFalse($this->cache->hasItem($this->getCacheKey()));
        $this->assertFalse($this->cache->getItem($this->getCacheKey())->isHit());
    }

    public function testItReturnsTheResponseStoredPreviouslyInCache()
    {
        $response = (object)['foo' => 'bar'];

        $this->apiClient->call(Argument::cetera())
            ->will(function () use ($response) {
                // The second call will return something else.
                // Later we verify that the first response was returned (so it was cached).
                $this->call(Argument::cetera())->willReturn((object)['a' => 'b']);

                return $response;
            });

        $this->cachingApiClient->call(
            self::CONTROLLER,
            self::HTTP_METHOD,
            self::ACTION,
            self::PARAMS,
            self::LANGUAGE,
            self::FILE
        );
        $actualResponse = $this->cachingApiClient->call(
            self::CONTROLLER,
            self::HTTP_METHOD,
            self::ACTION,
            self::PARAMS,
            self::LANGUAGE,
            self::FILE
        );

        $this->assertEquals($response, $actualResponse);
    }

    /**
     * @return string
     */
    private function getCacheKey()
    {
        return md5(self::CONTROLLER . self::HTTP_METHOD . self::ACTION . json_encode(self::PARAMS) . self::LANGUAGE . self::FILE);
    }
}
