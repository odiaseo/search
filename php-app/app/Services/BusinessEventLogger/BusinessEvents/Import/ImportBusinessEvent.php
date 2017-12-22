<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import;

use Psr\Log\LogLevel;

/**
 * All import events have the same document type.
 */
trait ImportBusinessEvent
{
    /**
     * @var string
     */
    protected $documentTypeLogKey = 'search_index_legacy';
    /**
     * @var string
     */
    protected $level = LogLevel::DEBUG;

    /**
     * @var array
     */
    protected $context;

    /**
     * The document type to log this to.
     *
     * @return string
     */
    final public function getMessage()
    {
        return $this->documentTypeLogKey;
    }

    /**
     * A log level as defined by LogLevel.
     *
     * @see \Psr\Log\LogLevel
     *
     * @return string
     */
    public function getLevel()
    {
        return$this->level;
    }

    /**
     * An array of JSON serializable object or exceptions or context that provide information about this business event.
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }
}
