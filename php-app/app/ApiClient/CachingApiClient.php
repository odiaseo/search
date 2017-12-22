<?php

namespace MapleSyrupGroup\Search\ApiClient;

use MapleSyrupGroup\Quidco\ApiClient\ClientInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Decorates the api client to cache its responses (useful in tests).
 */
class CachingApiClient implements ClientInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var int
     */
    private $lifetime;

    /**
     * @param ClientInterface          $client
     * @param CacheItemPoolInterface[] $cache
     * @param int                      $lifetime
     */
    public function __construct(ClientInterface $client, CacheItemPoolInterface $cache, $lifetime)
    {
        $this->client   = $client;
        $this->cache    = $cache;
        $this->lifetime = $lifetime;
    }

    /**
     * Makes a call to a specific v3 api service and stores the result in cache.
     *
     * @param string $controller
     * @param string $httpMethod
     * @param string $action
     * @param array  $params
     * @param string $language
     * @param string $file
     *
     * @return \stdClass
     */
    public function call(
        $controller,
        $httpMethod = ClientInterface::HTTP_GET,
        $action = '',
        $params = [],
        $language = 'en',
        $file = null
    )
    {
        $item = $this->findCachedResponse($controller, $httpMethod, $action, $params, $language, $file);

        if ($item->isHit()) {
            return $item->get();
        }

        $response = $this->client->call($controller, $httpMethod, $action, $params, $language, $file);

        $this->cacheResponse($item, $response);

        return $response;
    }

    /**
     * @param string $controller
     * @param string $httpMethod
     * @param string $action
     * @param array  $params
     * @param string $language
     * @param string $file
     *
     * @return CacheItemInterface
     */
    private function findCachedResponse($controller, $httpMethod, $action, $params, $language, $file)
    {
        $key = md5($controller . $httpMethod . $action . json_encode($params) . $language . $file);

        return $this->cache->getItem($key);
    }

    /**
     * @param CacheItemInterface $item
     * @param \stdClass          $response
     */
    private function cacheResponse(CacheItemInterface $item, \stdClass $response)
    {
        $item->set($response);
        $item->expiresAfter($this->lifetime);
        $this->cache->save($item);
    }
}
