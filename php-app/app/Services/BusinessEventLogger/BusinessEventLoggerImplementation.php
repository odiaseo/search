<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger;

use Psr\Log\LoggerInterface;

/**
 * Logs business events to a logger interface.
 */
class BusinessEventLoggerImplementation implements BusinessEventLogger
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * EventLoggerImplementation constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
    }

    /**
     * Log a business event.
     *
     * @param BusinessEvent $businessEvent
     */
    public function log(BusinessEvent $businessEvent)
    {
        $this->getLogger()->log(
            $businessEvent->getLevel(),
            $businessEvent->getMessage(),
            $businessEvent->getContext()
        );
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    private function setLogger($logger)
    {
        $this->logger = $logger;
    }
}
