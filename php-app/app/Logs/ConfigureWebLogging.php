<?php

namespace MapleSyrupGroup\Search\Logs;

use Monolog\Processor\WebProcessor;

/**
 * Specialised logging for web which adds web request information such as URLs etc.
 *
 * @package MapleSyrupGroup\Search\Logs
 */
class ConfigureWebLogging extends ConfigureLogging
{
    /**
     * Returns an array of callable objects that will be used as monolog processors
     *
     * @return object[]
     */
    protected function getMonologProcessors()
    {
        $preprocessors = parent::getMonologProcessors();

        $preprocessors[] = new WebProcessor();

        return $preprocessors;
    }
}
