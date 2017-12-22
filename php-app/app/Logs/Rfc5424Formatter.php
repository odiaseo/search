<?php

namespace MapleSyrupGroup\Search\Logs;

use Monolog\Formatter\FormatterInterface;
use Monolog\Logger;

/**
 * Output logs in Syslog RFC5424 format.
 */
class Rfc5424Formatter implements FormatterInterface
{
    /**
     * Prefix to SD-ID.
     */
    const STRUCTURED_DATA_PREFIX = 'monolog';

    const DATE_TIME_FORMAT = 'Y-m-d\\TH:i:s.uP';

    /**
     * Translates Monolog log levels to syslog log priorities.
     */
    protected $logLevels = [
        Logger::DEBUG     => LOG_DEBUG,
        Logger::INFO      => LOG_INFO,
        Logger::NOTICE    => LOG_NOTICE,
        Logger::WARNING   => LOG_WARNING,
        Logger::ERROR     => LOG_ERR,
        Logger::CRITICAL  => LOG_CRIT,
        Logger::ALERT     => LOG_ALERT,
        Logger::EMERGENCY => LOG_EMERG,
    ];

    /**
     * List of valid log facility names.
     */
    protected static $facilities = [
        'auth'     => LOG_AUTH,
        'authpriv' => LOG_AUTHPRIV,
        'cron'     => LOG_CRON,
        'daemon'   => LOG_DAEMON,
        'kern'     => LOG_KERN,
        'lpr'      => LOG_LPR,
        'mail'     => LOG_MAIL,
        'news'     => LOG_NEWS,
        'syslog'   => LOG_SYSLOG,
        'user'     => LOG_USER,
        'uucp'     => LOG_UUCP,
        'local0'   => 128, // LOG_LOCAL0
        'local1'   => 136, // LOG_LOCAL1
        'local2'   => 144, // LOG_LOCAL2
        'local3'   => 152, // LOG_LOCAL3
        'local4'   => 160, // LOG_LOCAL4
        'local5'   => 168, // LOG_LOCAL5
        'local6'   => 176, // LOG_LOCAL6
        'local7'   => 184, // LOG_LOCAL7
    ];

    /**
     * @var string
     */
    protected $format = self::DATE_TIME_FORMAT;

    /**
     * @var int|string
     */
    private $facility;

    /**
     * @var LogFormatter
     */
    private $formatter;

    /**
     * SyslogFormatter constructor.
     *
     * You can can also choose to indicate your logs are UTF8, and
     *
     * @param LogFormatter $formatter
     * @param int $facility
     * @param bool $isUnicode
     * @param bool $isStructured
     */
    public function __construct(LogFormatter $formatter, $facility = LOG_USER, $isUnicode = true, $isStructured = true)
    {
        $this->setFacility($facility);
        $this->setFormat($isStructured, $isUnicode);
        $this->setFormatter($formatter);
    }

    /**
     * @return LogFormatter
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * @param LogFormatter $formatter
     */
    private function setFormatter($formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Set log message format.
     *
     * @param bool $isStructured
     * @param bool $isUnicode
     */
    private function setFormat($isStructured, $isUnicode)
    {
        $this->format = '%datetime% ' . gethostname() . ' %channel% ' . getmypid();

        if ($isStructured) {
            $this->format .= ' - %structured-data%';
        } else {
            $this->format .= ' - -';
        }

        if ($isUnicode) {
            $this->format .= ' BOM';
        } else {
            $this->format .= ' %%';
        }

        $this->format .= ' %message% %context% %extra%';
    }

    /**
     * Add OS specific facilities and convert textual description of facility to syslog constant.
     *
     * @param $facility
     *
     * @throws \UnexpectedValueException
     */
    private function setFacility($facility)
    {
        if (is_string($facility)) {
            $facility = strtolower($facility);
        }

        if (array_key_exists($facility, self::$facilities)) {
            $facility = self::$facilities[$facility];
        } elseif (!in_array($facility, self::$facilities, true)) {
            throw new \UnexpectedValueException('Unknown facility value "' . $facility . '" given');
        }

        $this->facility = $facility;
    }

    /**
     * @param array $record
     *
     * @return string
     */
    public function format(array $record)
    {
        $vars               = $this->getFormatter()->normalizeArray($record);
        $output             = $this->format;
        $contextExtraString = '';

        foreach (['context', 'extra'] as $field) {
            $contextExtraString .= $this->getExtraContextString($field, $vars);

            if (empty($vars[$field])) {
                $output = str_replace('%' . $field . '%', '', $output);
            } else {
                $output = str_replace('%' . $field . '%', $this->stringify($vars[$field]), $output);
            }
        }

        if (empty($contextExtraString)) {
            $contextExtraString = '-';
        }

        $output = str_replace('%structured-data%', $contextExtraString, $output);

        foreach ($vars as $var => $val) {
            $output = str_replace('%' . $var . '%', $this->stringify($val), $output);
        }

        if (empty($record['level']) || empty($this->logLevels[$record['level']])) {
            return $output;
        }

        $output = '<' . ($this->logLevels[$record['level']] + $this->facility) . '>1 ' . $output;

        return $output;
    }

    /**
     * Return extra content passed to logger as string.
     *
     * @param string $field
     * @param array $data
     *
     * @return string
     */
    private function getExtraContextString($field, $data)
    {
        if (empty($data[$field])) {
            return '';
        }

        $format = '[' . static::STRUCTURED_DATA_PREFIX . ucfirst($field) . '@1 %s]';

        return sprintf($format, $this->getSaveValueString($data[$field]));
    }

    /**
     * Convert key-values pairs to string representation.
     *
     * @param $data
     *
     * @return string
     */
    private function getSaveValueString($data)
    {
        if (!is_array($data)) {
            return '';
        }

        $safeValueArray = [];

        foreach ($data as $var => $val) {
            $safeValue        = '"' . addcslashes($this->stringify($val), '"\\]') . '"';
            $safeValueArray[] = "$var=" . $safeValue;
        }

        return implode(' ', $safeValueArray);
    }

    /**
     * @param array $records
     *
     * @return string
     */
    public function formatBatch(array $records)
    {
        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }

    /**
     * Converts a value to a string (with new lines removed).
     *
     * @param mixed $value
     *
     * @return string
     */
    public function stringify($value)
    {
        return $this->replaceNewlines($this->convertToString($value));
    }

    /**
     * Convert a value to string for logging.
     *
     * @param mixed $data
     *
     * @return string
     */
    protected function convertToString($data)
    {
        if (null === $data || is_bool($data)) {
            return var_export($data, true);
        }

        if (is_scalar($data)) {
            return (string)$data;
        }

        return str_replace('\\/', '/', @json_encode($data));
    }

    /**
     * Replace the newlines with spaces.
     *
     * @param string $str
     *
     * @return string
     */
    protected function replaceNewlines($str)
    {
        return str_replace(["\r\n", "\r", "\n"], ' ', $str);
    }
}
