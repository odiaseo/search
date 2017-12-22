<?php
namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\DocumentInsert;

use MapleSyrupGroup\Search\Enums\Logging\IndexOperationEnum;
use Psr\Log\LogLevel;

class UnexpectedApiResponseTest extends \PHPUnit_Framework_TestCase
{

    private $indexName = 'merchant';
    private $type = 'alias';
    private $result = 10;
    /**
     * @var UnexpectedApiResponse
     */
    private $apiResponse;

    public function setUp()
    {
        $this->apiResponse = new UnexpectedApiResponse($this->result, $this->indexName, $this->type);

        parent::setUp();
    }

    public function testItExposesUnexpectedApiResponseDetailsAsContext()
    {
        $expectedResult = [
            IndexOperationEnum::OPERATION_TYPE_FIELD => IndexOperationEnum::OPERATION_TYPE_VALUE_INDEX,
            IndexOperationEnum::API_RESPONSE_FIELD   => $this->result,
            IndexOperationEnum::INDEX_FIELD          => $this->indexName,
            IndexOperationEnum::TYPE_FIELD           => $this->type,
            IndexOperationEnum::EVENT_MESSAGE_FIELD  => UnexpectedApiResponse::UNEXPECTED_RESPONSE_EVENT_MESSAGE
        ];

        $context = $this->apiResponse->getContext();

        $this->assertTrue(is_array($context));

        foreach ($expectedResult as $key => $value) {
            $this->assertArrayHasKey($key, $context, "$key was not found in context array");
            $this->assertSame($value, $context[$key]);
        }
    }

    public function testEventIsLoggedWithWarningLogLevel()
    {
        $this->assertSame(LogLevel::WARNING, $this->apiResponse->getLevel(), 'Invalid log level');
    }
}
