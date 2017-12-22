<?php

namespace MapleSyrupGroup\Search\Services\IndexStatusTracker;

/**
 * Determine if the index is running for the specified domain id.
 */
interface IndexStatusTracker
{
    /**
     * @param int $domainId
     * @param mixed $statusId
     *
     * @return bool
     */
    public function isRunning($domainId, $statusId = null);

    /**
     * Get the status of index being created e.g. when started etc.
     *
     * @param int $domainId
     * @param mixed $statusId
     *
     * @return StatusData
     */
    public function getStatus($domainId, $statusId = null);

    /**
     * Lock the indexing process, prevent same process from running.
     *
     * @param int $domainId
     * @param array $data
     * @param mixed $statusId
     *
     * @return bool
     */
    public function lock($domainId, array $data, $statusId = null);

    /**
     * Unlock process.
     *
     * @param int $domainId
     * @param mixed $statusId
     *
     * @return bool
     */
    public function unlock($domainId, $statusId = null);

    /**
     * Get a unique identifier for the index build process.
     *
     * @return string
     */
    public function getUniqueIdentifier();
}
