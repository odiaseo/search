<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use Psr\Log\LogLevel;

class FailureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Failure
     */
    private $failure;
    private $indexName = 'products';
    private $operationTime = 90;
    private $alias = 'alias';

    public function setUp()
    {
        /**
         * @var \Exception $exception
         */
        $exception = $this->prophesize('Exception')->reveal();

        $this->failure = new Failure(
            $this->indexName,
            $this->alias,
            $exception,
            $this->operationTime
        );

        parent::setUp();
    }

    public function testItExposesImportFailureDetailsAsContext()
    {
        $expectedResult = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => Failure::IMPORT_FAILURE_EVENT_MESSAGE,
            IndexOperationEnum::INDEX_FIELD          => $this->indexName,
            IndexOperationEnum::ALIAS_FIELD          => $this->alias,
            IndexOperationEnum::OPERATION_TIME_FIELD => $this->operationTime
        ];

        $context = $this->failure->getContext();

        $this->assertTrue(is_array($context));

        foreach ($expectedResult as $key => $value) {
            $this->assertArrayHasKey($key, $context, "$key was not found in context array");
            $this->assertSame($value, $context[$key]);
        }

        $this->assertInstanceOf(\Exception::class, $context[IndexOperationEnum::EXCEPTION_FIELD]);
    }

    public function testEventIsLoggedWithErrorLogLevel()
    {
        $this->assertSame(LogLevel::ERROR, $this->failure->getLevel(), 'Invalid log level');
    }
}
