<?php

namespace MapleSyrupGroup\Search\Exceptions\Stubs;

use Illuminate\Http\JsonResponse;
use MapleSyrupGroup\Search\Exceptions\Handler;

class HandlerStub extends Handler
{
    private $showTrace = false;

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     *
     * @return JsonResponse
     */
    public function render($request, \Exception $exception)
    {
        $payload    = $this->preparePayload($exception);
        $statusCode = $this->determineStatusCode($exception);

        return new JsonResponse($payload, $statusCode);
    }

    protected function showExceptionTrace()
    {
        return $this->showTrace;
    }

    /**
     * @param bool $showTrace
     */
    public function setShowTrace($showTrace)
    {
        $this->showTrace = $showTrace;
    }
}
