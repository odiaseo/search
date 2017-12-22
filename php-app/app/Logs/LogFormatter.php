<?php

namespace MapleSyrupGroup\Search\Logs;

use DateTime;
use Exception;
use JsonSerializable;

/**
 * Format log messages.
 */
class LogFormatter
{
    /**
     * @var string
     */
    public $dateFormat = Rfc5424Formatter::DATE_TIME_FORMAT;

    /**
     * @param mixed $data
     *
     * @return array|string
     */
    public function normalize($data)
    {
        if (is_array($data)) {
            return $this->normalizeArray($data);
        }

        if (is_resource($data)) {
            return sprintf('[resource] (%s)', get_resource_type($data));
        }

        return $this->normalizeOther($data);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function normalizeArray($data)
    {
        $normalized = [];
        $count      = 1;

        foreach ($data as $key => $value) {
            if ($count++ >= 1000) {
                return ['...' => 'Over 1000 items, aborting normalization'];
            }
            $normalized[$key] = $this->normalize($value);
        }

        return $normalized;
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    public function normalizeOther($data)
    {
        if (is_object($data)) {
            return $this->normalizeObject($data);
        }

        if (is_float($data)) {
            return $this->normalizeFloat($data);
        }

        return $this->normalizeUnknown($data);
    }

    /**
     * @param float $data
     *
     * @return string
     */
    public function normalizeFloat($data)
    {
        if (is_nan($data)) {
            return 'NaN';
        }

        if (is_infinite($data)) {
            return ($data > 0 ? '' : '-') . 'INF';
        }

        return $data;
    }

    /**
     * @param object $data
     *
     * @return string
     */
    public function normalizeObject($data)
    {
        if ($data instanceof DateTime) {
            return $data->format($this->dateFormat);
        }

        if ($data instanceof Exception) {
            return $this->normalizeException($data);
        }

        return $this->objectToJson($data);
    }

    /**
     * @param object $data
     *
     * @return mixed
     */
    public function objectToJson($data)
    {
        if (method_exists($data, '__toString') && !$data instanceof JsonSerializable) {
            $value = $data->__toString();
        } else {
            $value = json_encode($data);
        }

        return sprintf('[object] (%s: %s)', get_class($data), $value);
    }

    /**
     * @param Exception $exception
     *
     * @return string
     */
    public function normalizeException(Exception $exception)
    {
        $previousText   = '';
        $cloneException = $exception;

        /** @var Exception $previous */
        while ($previous = $cloneException->getPrevious()) {
            $previousText .= ', ' . get_class($previous);
            $previousText .= '(code: ' . $previous->getCode() . '): ';
            $previousText .= $previous->getMessage() . ' at ' . $previous->getFile();
            $previousText .= ':' . $previous->getLine();

            $cloneException = $previous;
        }

        $str = '[object] (' . get_class($exception) . '(code: ' . $exception->getCode() . '): ';
        $str .= $exception->getMessage() . ' at ';
        $str .= $exception->getFile() . ':' . $exception->getLine() . $previousText . ')';
        $str .= "\n[stacktrace]\n" . $exception->getTraceAsString();

        return $str;
    }

    /**
     * @param $data
     *
     * @return string
     */
    public function normalizeUnknown($data)
    {
        if (null === $data || is_scalar($data)) {
            return $data;
        }

        return '[unknown(' . gettype($data) . ')]';
    }
}
