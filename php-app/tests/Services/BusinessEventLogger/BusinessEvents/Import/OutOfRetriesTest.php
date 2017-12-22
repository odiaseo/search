<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use Psr\Log\LogLevel;

class OutOfRetriesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OutOfRetries
     */
    private $outOfRetries;
    private $alias = 'alias';
    private $retries = 3;
    private $totalRetries = 12;

    public function setUp()
    {
        /**
         * @var \Exception $exception
         */
        $exception = $this->prophesize('Exception')->reveal();

        $this->outOfRetries = new OutOfRetries(
            $this->alias,
            $exception,
            $this->retries,
            $this->totalRetries
        );
        parent::setUp();
    }

    public function testItExposesRetryExceededDetailsAsContext()
    {
        $expectedResult = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::ALIAS_FIELD          => $this->alias,
            IndexOperationEnum::RETRIES_LEFT_FIELD   => $this->retries,
            IndexOperationEnum::TOTAL_RETRIES        => $this->totalRetries,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => OutOfRetries::RETRY_EXCEEDED_EVENT_MESSAGE
        ];

        $context = $this->outOfRetries->getContext();

        $this->assertTrue(is_array($context));

        foreach ($expectedResult as $key => $value) {
            $this->assertArrayHasKey($key, $context, "$key was not found in context array");
            $this->assertSame($value, $context[$key]);
        }

        $this->assertInstanceOf(\Exception::class, $context[IndexOperationEnum::EXCEPTION_FIELD]);
    }

    public function testEventIsLoggedWithErrorLogLevel()
    {
        $this->assertSame(LogLevel::ERROR, $this->outOfRetries->getLevel(), 'Invalid log level');
    }

    public function testEventLogKey()
    {
        $this->assertSame('search_index_legacy', $this->outOfRetries->getMessage());
    }
}
