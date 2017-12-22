<?php

namespace MapleSyrupGroup\Search\Logs;

use Psr\Log\LogLevel;
use Symfony\Component\Console\Style\StyleInterface;

class SymfonyStyleLoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider loggerArgumentProvider
     * @group logger
     *
     * @param $styleMethod
     * @param $loggerMethod
     * @param $message
     * @param $level
     * @param $parameters
     */
    public function testLoggerLogsMessagesSuccessfully($styleMethod, $loggerMethod, $message, $level, $parameters)
    {
        /**
         * @var  StyleInterface $formatter
         */
        $formatter = $this->prophesize(StyleInterface::class);
        $formatter->$styleMethod($message)->willReturn([$level, $parameters]);
        $logger = new SymfonyStyleLogger($formatter->reveal());

        $return = $logger->$loggerMethod($message, $parameters);
        $this->assertSame([$level, $parameters], $return);
    }

    /**
     * @group logger
     */
    public function testLogMessageFormatWithParameters()
    {
        /**
         * @var  StyleInterface $formatter
         */
        $formatter  = $this->prophesize(StyleInterface::class);
        $logger     = new SymfonyStyleLogger($formatter->reveal());
        $parameters = ['test' => 1];

        $return = $logger->warning('message', $parameters);
        $this->assertNull($return);
    }

    public function loggerArgumentProvider()
    {
        return [
            ['error', 'critical', LogLevel::ERROR, 'message', []],
            ['error', 'alert', LogLevel::ERROR, 'message', []],
            ['error', 'error', LogLevel::ERROR, 'message', []],
            ['error', 'emergency', LogLevel::ERROR, 'message', []],

            ['warning', 'warning', LogLevel::ERROR, 'message', []],
            ['text', 'info', LogLevel::ERROR, 'message', []],
            ['text', 'debug', LogLevel::ERROR, 'message', []],
            ['text', 'notice', LogLevel::ERROR, 'message', []],
        ];
    }
}
