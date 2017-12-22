<?php

namespace MapleSyrupGroup\Search\Services\IndexStatusTracker;

use DateInterval;
use DateTime;
use InvalidArgumentException;
use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;

/**
 * Determine the status of the index using a lock file.
 */
class Lockfile implements IndexStatusTracker
{
    use StatusIdentifierTrait;

    /**
     * Location where the lock file will be stored.
     *
     * @var string
     */
    private $location;

    /**
     * Lifetime of the lock file.
     *
     * @var int
     */
    private $ttl;

    /**
     * @param string $location
     * @param int    $ttl
     */
    public function __construct($location, $ttl)
    {
        if (!is_writable($location)) {
            throw new InvalidArgumentException(
                sprintf('Lock file location [%s] is not writable', $location),
                ExceptionCodes::CODE_INVALID_ARGUMENT
            );
        }

        $this->setLocation($location);
        $this->setTtl($ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function isRunning($domainId, $statusId = null)
    {
        if (!is_file($this->getFilename($domainId, $statusId))) {
            return false;
        }

        if (time() - filemtime($this->getFilename($domainId, $statusId)) > $this->getTtl()) {
            $this->unlock($domainId);

            return false;
        }

        return true;
    }

    /**
     * @param int  $domainId
     * @param null $statusId
     *
     * @return StatusData
     */
    public function getStatus($domainId, $statusId = null)
    {
        $filename = $this->getFilename($domainId, $statusId);

        if (!is_file($filename)) {
            return new StatusData();
        }

        $data     = json_decode(file_get_contents($filename), true);
        $expireAt = (new DateTime($data['createdAt']))->add(new DateInterval(sprintf('PT%dS', $this->getTtl())));
        $data     = array_merge(
            $data,
            [
                'storage' => $filename,
                'hint'    => sprintf(
                    'Delete lock file to force indexing, lock file will be auto deleted at %s',
                    $expireAt->format('c')
                ),
            ]
        );

        return new StatusData($data['uniqueId'], $data['status'], $data['createdAt'], $data['storage'], $data['hint']);
    }

    /**
     * Create lock file which meta data.
     *
     * @param int   $domainId
     * @param array $data
     * @param null  $statusId
     *
     * @return bool
     */
    public function lock($domainId, array $data, $statusId = null)
    {
        $data = array_merge(
            $data,
            [
                'status'    => 'Index running for domain ' . $domainId,
                'createdAt' => (new DateTime())->format('c'),
                'uniqueId'  => $statusId,
            ]
        );

        file_put_contents($this->getFilename($domainId, $statusId), json_encode($data, JSON_PRETTY_PRINT));

        return true;
    }

    /**
     * Remove lock file.
     *
     * @param int  $domainId
     * @param null $statusId
     *
     * @return bool
     */
    public function unlock($domainId, $statusId = null)
    {
        $filename = $this->getFilename($domainId, $statusId);
        if (is_file($filename)) {
            return unlink($filename);
        }

        return false;
    }

    /**
     * @param $domainId
     * @param mixed $statusId
     *
     * @return mixed
     */
    public function getFilename($domainId, $statusId)
    {
        if (empty($domainId)) {
            $domainId = 'all';
        } else {
            $domainId = (int) strip_tags($domainId);
        }

        $statusId   = $this->validateIdentifier($statusId);
        $identifier = trim($domainId . '-' . $statusId, ' -');

        return sprintf('%s/index_%s.lock', $this->getLocation(), hash('crc32', $identifier));
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return int
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * @param int $ttl
     */
    public function setTtl($ttl)
    {
        $this->ttl = (int) $ttl;
    }
}
