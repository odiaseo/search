<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class BusinessEventLoggerImplementationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group logger
     */
    public function testBusinessEventLoggerCanLogEvents()
    {
        $level = LogLevel::DEBUG;
        $message = 'error';
        $context = [];

        $logger = $this->prophesize(LoggerInterface::class);
        $event = $this->prophesize(BusinessEvent::class);

        $event->getLevel()->willReturn($level);
        $event->getMessage()->willReturn($message);
        $event->getContext()->willReturn($context);

        $logger->log($level, $message, $context)->willReturn(true);

        $eventLogger = new BusinessEventLoggerImplementation($logger->reveal());
        $return = $eventLogger->log($event->reveal());

        $this->assertNull($return);

    }
}
