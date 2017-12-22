<?php

namespace MapleSyrupGroup\Search\Services\IndexStatusTracker;

use DateTime;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Stores index status information.
 */
class StatusData implements Arrayable
{
    const DEFAULT_STATUS = 'Not found';

    /**
     * @var string
     */
    private $uniqueId;

    /**
     * @var string
     */
    private $status = self::DEFAULT_STATUS;

    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @var string
     */
    private $storage;

    /**
     * @var string
     */
    private $hint;

    /**
     * StatusData constructor.
     *
     * @param string $uniqueId
     * @param string $status
     * @param string $createAt
     * @param string $storage
     * @param string $hint
     */
    public function __construct($uniqueId = '', $status = '', $createAt = '', $storage = '', $hint = '')
    {
        $this->setUniqueId($uniqueId);
        $this->setStatus($status);
        $this->setCreatedAt($createAt);
        $this->setStorage($storage);
        $this->setHint($hint);
    }

    /**
     * @param string $uniqueId
     *
     * @return StatusData
     */
    public function setUniqueId($uniqueId)
    {
        $this->uniqueId = $uniqueId;

        return $this;
    }

    /**
     * @param mixed $status
     *
     * @return StatusData
     */
    public function setStatus($status)
    {
        if ($status) {
            $this->status = $status;
        }

        return $this;
    }

    /**
     * @param string $createdAt
     *
     * @return StatusData
     */
    public function setCreatedAt($createdAt)
    {
        if ($createdAt) {
            $this->createdAt = new DateTime($createdAt);
        }

        return $this;
    }

    /**
     * @param string $storage
     *
     * @return StatusData
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * @param string $hint
     *
     * @return StatusData
     */
    public function setHint($hint)
    {
        $this->hint = $hint;

        return $this;
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id'         => $this->getUniqueId(),
            'status'     => $this->getStatus(),
            'created_at' => $this->getCreatedAt() ? $this->getCreatedAt()->format('c') : '',
            'storage'    => $this->getStorage(),
            'hint'       => $this->getHint(),
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
}
