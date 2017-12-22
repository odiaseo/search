<?php
namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\DocumentInsert;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use Psr\Log\LogLevel;

class ValidationFailedTest extends \PHPUnit_Framework_TestCase
{

    private $indexName = 'merchant';
    private $type = 'alias';
    private $actual = 10;
    private $expected = 20;
    /**
     * @var ValidationFailed
     */
    private $validationFailed;

    public function setUp()
    {
        $this->validationFailed = new ValidationFailed(
            $this->indexName,
            $this->type,
            $this->expected,
            $this->actual
        );

        parent::setUp();
    }

    public function testItExposesValidationFailureDetailsAsContext()
    {
        $expectedResult = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::INDEX_FIELD          => $this->indexName,
            IndexOperationEnum::EXPECTED_VALUE_FIELD => $this->expected,
            IndexOperationEnum::ACTUAL_VALUE_FIELD   => $this->actual,
            IndexOperationEnum::TYPE_FIELD           => $this->type,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => ValidationFailed::VALIDATION_FAILURE_EVENT_MESSAGE,
        ];

        $context = $this->validationFailed->getContext();

        $this->assertTrue(is_array($context));

        foreach ($expectedResult as $key => $value) {
            $this->assertArrayHasKey($key, $context, "$key was not found in context array");
            $this->assertSame($value, $context[$key]);
        }
    }

    public function testEventIsLoggedWithWarningLogLevel()
    {
        $this->assertSame(LogLevel::WARNING, $this->validationFailed->getLevel(), 'Invalid log level');
    }
}
