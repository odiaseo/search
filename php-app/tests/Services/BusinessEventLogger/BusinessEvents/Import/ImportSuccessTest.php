<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use Psr\Log\LogLevel;

class ImportSuccessTest extends \PHPUnit_Framework_TestCase
{

    private $indexName = 'merchant';
    private $indexAlias = 'alias';
    private $duration = 10.0;
    private $oldIndexes = [];
    private $count = 1;
    /**
     * @var  Success
     */
    private $success;

    public function setUp()
    {
        $this->success = new Success(
            $this->indexName,
            $this->indexAlias,
            $this->oldIndexes,
            $this->duration,
            $this->count
        );
        parent::setUp();
    }

    public function testItExposesImportSuccessDetailsAsContext()
    {
        $expectedResult = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::INDEX_FIELD          => $this->indexName,
            IndexOperationEnum::ALIAS_FIELD          => $this->indexAlias,
            IndexOperationEnum::REPLACED_INDEX_FIELD => $this->oldIndexes,
            IndexOperationEnum::OPERATION_TIME_FIELD => $this->duration,
            IndexOperationEnum::DOCUMENT_COUNT_FIELD => $this->count,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => Success::SUCCESS_EVENT_MESSAGE,
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
