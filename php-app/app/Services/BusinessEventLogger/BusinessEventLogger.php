<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger;

/**
 * Logs business events
 *
 * @package MapleSyrupGroup\Search\Services\BusinessEventLogger
 */
interface BusinessEventLogger
{
    /**
     * Log a business event
     *
     * @param BusinessEvent $businessEvent
     */
    public function log(BusinessEvent $businessEvent);
}
