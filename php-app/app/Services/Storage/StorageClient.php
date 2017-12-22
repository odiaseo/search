<?php

namespace MapleSyrupGroup\Search\Services\Storage;

/**
 * Determine if the index is running for the specified domain id.
 */
interface StorageClient
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * @param string $key
     * @param string $value
     * @param int    $expireInSeconds
     *
     * @return bool
     */
    public function set($key, $value, $expireInSeconds);

    /**
     * @param string $key
     * @param int    $seconds
     *
     * @return int
     */
    public function expire($key, $seconds);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function exists($key);

    /**œ
     * @param string $key
     *
     * @return bool
     */
    public function delete($key);
}
