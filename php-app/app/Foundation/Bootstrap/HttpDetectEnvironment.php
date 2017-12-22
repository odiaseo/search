<?php

namespace MapleSyrupGroup\Search\Foundation\Bootstrap;

/**
 * Detect environment for HTTP requests
 */
class HttpDetectEnvironment extends ConsoleDetectEnvironment
{
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    protected function isHttpTestMode()
    {
        return request('testing', false) ? true : false;
    }
}
