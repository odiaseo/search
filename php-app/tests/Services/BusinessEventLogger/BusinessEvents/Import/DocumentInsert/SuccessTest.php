<?php
namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\DocumentInsert;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use Psr\Log\LogLevel;

class SuccessTest extends \PHPUnit_Framework_TestCase
{

    private $indexName = 'merchant';
    private $typeName = 'alias';
    private $operationTime = 10.0;
    private $total = 20;
    /**
     * @var  Success
     */
    private $success;

    public function setUp()
    {
        $this->success = new Success($this->indexName, $this->typeName, $this->total, $this->operationTime);
        parent::setUp();
    }

    public function testItExposesSuccessDetailsAsContext()
    {
        $expectedResult = [
            IndexOperationEnum::INDEX_FIELD          => $this->indexName,
            IndexOperationEnum::TYPE_FIELD           => $this->typeName,
            IndexOperationEnum::OPERATION_TIME_FIELD => $this->operationTime,
            IndexOperationEnum::DOCUMENT_COUNT_FIELD => $this->total,
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => Success::IMPORT_SUCCESS_MESSAGE
        ];

        $context = $this->success->getContext();

        $this->assertTrue(is_array($context));

        foreach ($expectedResult as $key => $value) {
            $this->assertArrayHasKey($key, $context, "$key was not found in context array");
            $this->assertSame($value, $context[$key]);
        }
    }

    public function testEventIsLoggedWithDebugLogLevel()
    {
        $this->assertSame(LogLevel::DEBUG, $this->success->getLevel(), 'Invalid log level');
    }
}
