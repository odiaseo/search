<?php
namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\DocumentInsert;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use Psr\Log\LogLevel;

class MerchantCountChangedTest extends \PHPUnit_Framework_TestCase
{

    private $indexName = 'merchant';
    private $type = 'alias';
    private $actual = 10;
    private $expected = 20;
    /**
     * @var MerchantCountChanged
     */
    private $merchantCountChanged;

    public function setUp()
    {
        $this->merchantCountChanged = new MerchantCountChanged(
            $this->indexName,
            $this->type,
            $this->expected,
            $this->actual
        );

        parent::setUp();
    }

    public function testItExposesMerchantCountChangedDetailsAsContext()
    {
        $expectedResult = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::INDEX_FIELD          => $this->indexName,
            IndexOperationEnum::EXPECTED_VALUE_FIELD => $this->expected,
            IndexOperationEnum::ACTUAL_VALUE_FIELD   => $this->actual,
            IndexOperationEnum::TYPE_FIELD           => $this->type,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => MerchantCountChanged::COUNT_CHANGED_EVENT_MESSAGE
        ];

        $context = $this->merchantCountChanged->getContext();

        $this->assertTrue(is_array($context));

        foreach ($expectedResult as $key => $value) {
            $this->assertArrayHasKey($key, $context, "$key was not found in context array");
            $this->assertSame($value, $context[$key]);
        }

    }

    public function testEventIsLoggedWithWarningLogLevel()
    {
        $this->assertSame(LogLevel::WARNING, $this->merchantCountChanged->getLevel(), 'Invalid log level');
    }
}
