<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use Psr\Log\LogLevel;

class RetryOccurredTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RetryOccurred
     */
    private $retryOccurred;
    private $alias = 'alias';
    private $retries = 3;
    private $totalRetries = 0;
    private $waitTime = 10;

    public function setUp()
    {
        /**
         * @var \Exception $exception
         */
        $exception = $this->prophesize('Exception')->reveal();

        $this->retryOccurred = new RetryOccurred(
            $this->alias,
            $exception,
            $this->retries,
            $this->totalRetries,
            $this->waitTime
        );
        parent::setUp();
    }

    public function testItExposesRetyAttemptDetailsAsContext()
    {
        $expectedResult = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::ALIAS_FIELD          => $this->alias,
            IndexOperationEnum::RETRIES_LEFT_FIELD   => $this->retries,
            IndexOperationEnum::TOTAL_RETRIES        => $this->totalRetries,
            IndexOperationEnum::SECONDS_TO_RETRY     => $this->waitTime,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => RetryOccurred::RETRY_EVENT_MESSAGE
        ];

        $context = $this->retryOccurred->getContext();

        $this->assertTrue(is_array($context));

        foreach ($expectedResult as $key => $value) {
            $this->assertArrayHasKey($key, $context, "$key was not found in context array");
            $this->assertSame($value, $context[$key]);
        }

        $this->assertInstanceOf(\Exception::class, $context[IndexOperationEnum::EXCEPTION_FIELD]);
    }

    public function testEventIsLoggedWithWarningLogLevel()
    {
        $this->assertSame(LogLevel::WARNING, $this->retryOccurred->getLevel(), 'Invalid log level');
    }
}
