<?php

namespace MapleSyrupGroup\Search\Logs;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Style\StyleInterface;

/**
 * Takes a Symfony Style Object that you might get from a console input stream and wrap it in a loggers interface
 *
 * @package MapleSyrupGroup\Search\Logs
 */
class SymfonyStyleLogger implements LoggerInterface
{
    /**
     * @var StyleInterface
     */
    private $styleInterface;

    /**
     * @var array
     */
    private $errorLevels = [
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::EMERGENCY,
        LogLevel::ERROR,
    ];

    /**
     * ConsoleOutputLogger constructor.
     *
     * @param StyleInterface $outputInterface
     */
    public function __construct(StyleInterface $outputInterface)
    {
        $this->styleInterface = $outputInterface;
    }


    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        return $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     *
     * @return mixed
     */
    public function alert($message, array $context = array())
    {
        return $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     *
     * @return mixed
     */
    public function critical($message, array $context = array())
    {
        return $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     *
     * @return mixed
     */
    public function error($message, array $context = array())
    {
        return $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     *
     * @return mixed
     */
    public function warning($message, array $context = array())
    {
        return $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return mixed
     */
    public function notice($message, array $context = array())
    {
        return $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     *
     * @return mixed
     */
    public function info($message, array $context = array())
    {
        return $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return mixed
     */
    public function debug($message, array $context = array())
    {
        return $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return mixed
     */
    public function log($level, $message, array $context = array())
    {
        if (in_array($level, $this->errorLevels)) {
            return $this->outputError($message, $context);
        } elseif ($level === LogLevel::WARNING) {
            return $this->outputWarning($message, $context);
        } else {
            return $this->outputInfo($message, $context);
        }
    }

    /**
     * Output to stdout
     *
     * @param string $message
     * @param array $content
     * @return mixed
     */
    private function outputInfo($message, $content = array())
    {
        return $this->styleInterface->text($this->formatContent($message, $content));
    }

    /**
     * Output error
     *
     * @param       $message
     * @param array $content
     */
    private function outputError($message, $content = array())
    {
        return $this->styleInterface->error($this->formatContent($message, $content));
    }

    /**
     * Output warning
     *
     * @param $message
     * @param array $content
     * @return mixed
     */
    private function outputWarning($message, $content = array())
    {
        return $this->styleInterface->warning($this->formatContent($message, $content));
    }

    /**
     * Format the message into something human
     *
     * @param string $message
     * @param array $content
     *
     * @return mixed
     */
    private function formatContent($message, $content = array())
    {
        if (empty($content)) {
            return $message;
        }

        $formattedContent = [$message];

        foreach ($content as $element) {
            $formattedContent[] = (string)$element;
        }

        return $formattedContent;
    }
}
