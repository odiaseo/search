<?php

namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use Psr\Log\LogLevel;

class StartedTest extends \PHPUnit_Framework_TestCase
{

    private $indexName = 'merchant';
    private $indexAlias = 'alias';


    public function testItExposesImportStartedDetailsAsContext()
    {
        $expectedResult = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::INDEX_FIELD          => $this->indexName,
            IndexOperationEnum::ALIAS_FIELD          => $this->indexAlias,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => Started::INDEX_STARTED_EVENT_MESSAGE
        ];

        $success = new Started($this->indexName, $this->indexAlias);
        $context = $success->getContext();

        $this->assertTrue(is_array($context));

        foreach ($expectedResult as $key => $value) {
            $this->assertArrayHasKey($key, $context, "$key was not found in context array");
            $this->assertSame($value, $context[$key]);
        }
    }

    public function testEventIsLoggedWithDebugLogLevel()
    {
        $success = new Started($this->indexName, $this->indexAlias);
        $this->assertSame(LogLevel::DEBUG, $success->getLevel(), 'Invalid log level');
    }
}
