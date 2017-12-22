<?php

namespace MapleSyrupGroup\Search\Http\Request;

use MapleSyrupGroup\QCommon\Http\Requests\ApiRequest as CommonApiRequest;

interface ApiRequest extends CommonApiRequest
{
    /**
     * Retrieve a query string item from the request.
     *
     * @param  string $key
     * @param  string|array|null $default
     *
     * @return string|array
     */
    public function query($key = null, $default = null);
}
