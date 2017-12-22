<?php
namespace MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Search;

use MapleSyrupGroup\Search\Enums\Logging\MerchantSearchEnum;
use Psr\Log\LogLevel;

class SearchCompletedEventTest extends \PHPUnit_Framework_TestCase
{

    private $term = 'amazon';
    private $searchStrategy = 'best_match';
    private $elapsedSeconds = 3600;

    /** @var SearchCompletedEvent */
    private $completedEvent;


    /**
     * @dataProvider searchResultProvider
     * @param array $response
     * @param int $total
     * @param int $limit
     */
    public function testItExposesCompletedSearchEventDetailsAsContext($response, $limit, $total)
    {
        $expectedKeys = [
            MerchantSearchEnum::SEARCH_INSTANCE_ID_FIELD,
            MerchantSearchEnum::NUM_MERCHANTS_FIELD,
            MerchantSearchEnum::SEARCH_PLATFORM_FIELD,
            MerchantSearchEnum::SEARCH_RESULTS_FIELD,
            MerchantSearchEnum::SEARCH_RESULTS_NAMES_FIELD,
            MerchantSearchEnum::SEARCH_STRATEGY_FIELD,
            MerchantSearchEnum::SEARCH_TERM_FIELD,
            MerchantSearchEnum::SEARCH_TIME_FIELD,
            MerchantSearchEnum::SEARCH_TOP_RESULT_FIELD,
            MerchantSearchEnum::SEARCH_TOP_RESULT_NAME_FIELD,
            MerchantSearchEnum::SEARCH_TYPE_FIELD,
            MerchantSearchEnum::VERSION_FIELD,
            MerchantSearchEnum::USER_ID_FIELD
        ];

        $this->completedEvent = new SearchCompletedEvent(
            $this->term,
            $response,
            $this->searchStrategy,
            $this->elapsedSeconds
        );

        $context = $this->completedEvent->getContext();

        $this->assertTrue(is_array($context));

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $context, "$key was not found in context array");
        }

        $this->assertSame(
            [
                $this->term,
                $this->searchStrategy,
                MerchantSearchEnum::SEARCH_PLATFORM_VALUE,
                MerchantSearchEnum::SEARCH_TYPE_VALUE,
                MerchantSearchEnum::VERSION_VALUE,
                MerchantSearchEnum::USER_ID_VALUE,
                LogLevel::DEBUG,
                SearchCompletedEvent::EVENT_MESSAGE,
            ],
            [
                $context[MerchantSearchEnum::SEARCH_TERM_FIELD],
                $context[MerchantSearchEnum::SEARCH_STRATEGY_FIELD],
                $context[MerchantSearchEnum::SEARCH_PLATFORM_FIELD],
                $context[MerchantSearchEnum::SEARCH_TYPE_FIELD],
                $context[MerchantSearchEnum::VERSION_FIELD],
                $context[MerchantSearchEnum::USER_ID_FIELD],
                $this->completedEvent->getLevel(),
                $this->completedEvent->getMessage()
            ]
        );

        if ($total == 0) {
            $this->assertNull($context[MerchantSearchEnum::SEARCH_TOP_RESULT_FIELD]);
            $this->assertNull($context[MerchantSearchEnum::SEARCH_TOP_RESULT_NAME_FIELD]);
            $this->assertEmpty($context[MerchantSearchEnum::SEARCH_RESULTS_FIELD]);
            $this->assertEmpty($context[MerchantSearchEnum::SEARCH_RESULTS_NAMES_FIELD]);
        } else {
            $this->assertNotEmpty($context[MerchantSearchEnum::SEARCH_TOP_RESULT_FIELD]);
            $this->assertNotEmpty($context[MerchantSearchEnum::SEARCH_RESULTS_FIELD]);
            $this->assertTrue(is_array($context[MerchantSearchEnum::SEARCH_RESULTS_FIELD]));

            $this->assertNotEmpty($context[MerchantSearchEnum::SEARCH_TOP_RESULT_NAME_FIELD]);
            $this->assertNotEmpty($context[MerchantSearchEnum::SEARCH_RESULTS_NAMES_FIELD]);
            $this->assertTrue(is_array($context[MerchantSearchEnum::SEARCH_RESULTS_NAMES_FIELD]));

            $this->assertEquals($limit, count($context[MerchantSearchEnum::SEARCH_RESULTS_FIELD]));
            $this->assertEquals($limit, count($context[MerchantSearchEnum::SEARCH_RESULTS_NAMES_FIELD]));
        }

        $this->assertEquals($total, $context[MerchantSearchEnum::NUM_MERCHANTS_FIELD]);
    }


    public function searchResultProvider()
    {
        return [
            [
                $this->generateSearchResult(0),
                0,
                0
            ],
            [
                $this->generateSearchResult(50),
                SearchCompletedEvent::LIMIT,
                50
            ],
            [
                [], //invalid results
                0,
                0
            ],
            [
                [
                    SearchCompletedEvent::HITS_KEY => [
                        SearchCompletedEvent::HITS_KEY => null, //invalid result
                    ]
                ],
                0,
                0
            ],
            [
                [
                    SearchCompletedEvent::HITS_KEY => [
                        SearchCompletedEvent::HITS_KEY => [
                            [
                                'invalid_source_key' //no _source key
                            ]
                        ]
                    ]
                ],
                0,
                0
            ],
            [
                [
                    SearchCompletedEvent::HITS_KEY => [
                        SearchCompletedEvent::HITS_KEY => [
                            [
                                SearchCompletedEvent::SOURCE_KEY => [
                                    'invalid_id'   => 1, //no id field
                                    'invalid_name' => 'bad_name', //no name field
                                ]
                            ]
                        ]
                    ]
                ],
                0,
                0
            ]
        ];

    }

    private function generateSearchResult($limit)
    {
        $items = [];
        for ($count = 0; $count < $limit; $count++) {
            $items[] = [
                SearchCompletedEvent::SOURCE_KEY => [
                    SearchCompletedEvent::ID_FIELD   => $count + 1,
                    SearchCompletedEvent::NAME_FIELD => 'merchant name ' . $count
                ]
            ];
        }

        $results = [
            SearchCompletedEvent::HITS_KEY => [
                SearchCompletedEvent::HITS_KEY  => $items,
                SearchCompletedEvent::TOTAL_KEY => count($items)
            ]
        ];

        return $results;
    }
}
