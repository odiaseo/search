<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger;

/**
 * A Business Event
 *
 * @package MapleSyrupGroup\Search\Services\BusinessEventLogger
 */
interface BusinessEvent
{
    /**
     * A log level as defined by LogLevel
     *
     * @see \Psr\Log\LogLevel
     *
     * @return string
     */
    public function getLevel();

    /**
     * A human readable string for this particular event.
     *
     * @return string
     */
    public function getMessage();

    /**
     * An array of JSON serializable object or exceptions or context that provide information about this business event
     *
     * @return array
     */
    public function getContext();
}
