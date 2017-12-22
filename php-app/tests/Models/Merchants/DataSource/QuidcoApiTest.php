<?php

namespace MapleSyrupGroup\Search\Models\Merchants\DataSource;

use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantFilterAggregate;
use MapleSyrupGroup\Search\Models\Merchants\Filters\NameFilter;
use MapleSyrupGroup\Search\Models\Merchants\Filters\StopWordFilter;
use MapleSyrupGroup\Search\Models\Merchants\SearchableMerchantModel;
use MapleSyrupGroup\Search\Models\SearchableModelMapper;
use Prophecy\Argument;

class QuidcoApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group model
     * @expectedException \MapleSyrupGroup\Quidco\ApiClient\Exception\ApiErrorException
     */
    public function testThatExceptionIsThrownWithMissingSearchResultKey()
    {
        $response   = (object)[
            'merchants' => (object)[],
        ];
        $parameters = [
            'page'      => 1,
            'page_size' => 100,
        ];
        $httpClient = $this->prophesize(\MapleSyrupGroup\Quidco\ApiClient\Client::class);
        $filter     = $this->prophesize(MerchantFilterAggregate::class);
        $httpClient->call('merchant', 'get', 'enriched', $parameters)->willReturn($response);

        $mapper = $this->prophesize(SearchableModelMapper::class);
        $mapper->getMappingProperties()->willReturn([]);

        $api = new QuidcoApi($httpClient->reveal(), $mapper->reveal(), $filter->reveal());
        $api->all();
    }

    /**
     * @group model
     * @dataProvider pageParametersProvider
     */
    public function testThatTheQuidcoApiReturnsPaginatedSearchResult($page, $pageSize, $totalResult, $numberOfPages)
    {
        $expected = [
            'result' => [],
            'total'  => $totalResult,
            'pages'  => $numberOfPages,
        ];

        $params = [
            'page'      => $page,
            'page_size' => $pageSize,
        ];

        $model    = $this->getModel($params, $totalResult, $numberOfPages);
        $response = $model->all($page, $pageSize);
        $this->assertSame($expected, $response);
        $this->assertSame([], $model->getMappingProperties());
    }

    /**
     * @dataProvider merchantDataProvider
     * @group model
     *
     * @param $merchant
     */
    public function testThatMerchantSearchResultCanBeConverted($merchant)
    {
        $params = [
            'page'      => 1,
            'page_size' => 1,
        ];

        $model    = $this->getModel($params, 1, 1);
        $document = $model->toDocumentArray($merchant);

        foreach ($model->getMerchantFields() as $field) {
            $this->assertArrayHasKey($field, $document);
        }

        foreach ($document[$model::FIELD_CATEGORIES] as $category) {
            $this->assertInstanceOf(\stdClass::class, $category);
            $this->assertInternalType('string', $category->name);
            $this->assertInternalType('integer', $category->weight);
        }

        $this->assertNotEmpty($document[$model::FIELD_KEYWORDS]);
        foreach ($document[$model::FIELD_KEYWORDS] as $keyword) {
            $this->assertInternalType('array', $keyword);
        }

        foreach ($document[$model::FIELD_IMAGES] as $image) {
            $this->assertInstanceOf(\stdClass::class, $image);
            foreach ($model->getImageFields() as $field) {
                $this->assertArrayHasKey($field, (array)$image);
            }
        }
    }

    /**
     * @dataProvider merchantRateTextDataProvider
     * @group model
     *
     * @param array $data
     * @param array $expected
     */
    public function testThatStopWordsAreRemovedFromRatesText($data, $expected)
    {
        $params = [
            'page'      => 1,
            'page_size' => 1,
        ];

        $stopWords = [
            'english' => [
                'rates_text' => [
                    'for',
                    'all',
                    'at',
                    'sales',
                    'month',
                    'the',
                    'off',
                    'with',
                ],
            ],
        ];

        $merchant = [
            SearchableMerchantModel::FIELD_RATES_TEXT_FILTERED_ACTIVE  => $data,
            SearchableMerchantModel::FIELD_RATES_TEXT_FILTERED_EXPIRED => $data,
        ];

        $model    = $this->getModel($params, 1, 1, $stopWords, $expected);
        $document = $model->toDocumentArray($merchant);

        $this->assertArrayHasKey(SearchableMerchantModel::FIELD_RATES_TEXT_FILTERED_EXPIRED, $document);
        $this->assertArrayHasKey(SearchableMerchantModel::FIELD_RATES_TEXT_FILTERED_ACTIVE, $document);
        $this->assertSame($document[SearchableMerchantModel::FIELD_RATES_TEXT_FILTERED_ACTIVE], $expected);
        $this->assertSame($document[SearchableMerchantModel::FIELD_RATES_TEXT_FILTERED_EXPIRED], $expected);
    }

    public function merchantRateTextDataProvider()
    {
        return [
            [
                [
                    'for the Surface Pro 4 sales',
                    'for Office 365 yearly subscriptions with 70% off Kaspersky',
                    "for the Samsung Gear S2 for all sales in the 'Spring Clean Campaign'",
                ],
                [
                    'office 365 yearly subscriptions 70% kaspersky',
                    'samsung gear s2 in spring clean campaign',
                    'surface pro 4',
                ],
            ],
            [
                null,
                [],
            ],
            [
                [
                    '      for sales',
                    ' sales month',
                    'for all lumia at nokia 365 ',
                    'for £10.50 sales campaign',
                    'for x-box v1.4 sales     monthly',
                ],
                [
                    'campaign',
                    'lumia nokia 365',
                    'x-box v1.4 monthly',
                ],
            ],
            [
                [
                    'best rates',
                    '10% discount',
                    '£20 cashback',
                ],
                [
                    '10% discount',
                    'best rates',
                    'cashback',
                ],
            ],
        ];
    }

    public function merchantDataProvider()
    {
        return [
            [
                [
                    'images'                      => [
                        [
                            ContentApi::FIELD_WIDTH  => 300,
                            ContentApi::FIELD_HEIGHT => 250,
                        ],
                    ],
                    'categories'                  => [
                        [
                            'name'     => 'Fashion',
                            'weight'   => 3,
                            'synonyms' => null,
                        ],
                    ],
                    'related'                     => [
                        [
                            'merchant_id'         => 1,
                            'related_merchant_id' => 10,
                            'name'                => 'Tesco',
                            'type'                => 'merchant',
                            'url_name'            => 'tesco.com',
                        ],
                    ],
                    'rates_text_filtered_active'  => [],
                    'rates_text_filtered_expired' => null,
                    'keywords'                    => [
                        ['keywords' => 'fashion'],
                    ],
                ],
            ],
            [
                [
                    'categories'                  => [
                        [
                            'id'       => 4,
                            'name'     => 'Fashion',
                            'url_name' => 'category',
                            'weight'   => 5,
                            'synonyms' => [
                                1234,
                                'vogue',
                            ],
                        ],
                    ],
                    'images'                      => [

                    ],
                    'rates_text_filtered_active'  => null,
                    'rates_text_filtered_expired' => [],
                    'keywords'                    => [
                        ['keywords' => 'shirts'],
                        ['keywords' => 'food'],
                    ],
                ],
            ],
            [
                [
                    'id'                         => 2,
                    'description'                => ' <div>   Marks &nbsp;   Spencers &pound; <br /></div>',
                    'rates_text'                 => 'Best rate',
                    'rates_text_filtered_active' => [
                        'best rates',
                        '10% discount off',
                        '£20 cach back',
                    ],
                    'related_merchant_rates'     => [
                        ['best_match' => 23.50],
                        ['next' => 89.45],
                    ],
                    'keywords'                   => [
                        ['keywords' => 'electronics'],
                    ],
                    'similar'                    => [
                        [
                            'merchant_id'         => 1,
                            'similar_merchant_id' => 10,
                            'name'                => 'Tesco',
                            'type'                => 'merchant',
                            'url_name'            => 'tesco.com',
                        ],
                        [
                            'merchant_id'         => 2,
                            'similar_merchant_id' => 10,
                            'name'                => 'Tesco',
                            'type'                => 'merchant',
                            'url_name'            => 'tesco.com',
                            'in_store'            => 1,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function pageParametersProvider()
    {
        return [
            [null, null, 0, 0],
            [0, 0, 0, 0],
            [1, 1, 1, 10],
        ];
    }

    private function getModel($parameters, $total, $numPages, $stopWords = [], $results = [])
    {
        $response = (object)[
            'merchants' => (object)[],
            'meta'      => (object)[
                'pagination' => (object)[
                    'total'       => $total,
                    'total_pages' => $numPages,
                ],
            ],
        ];

        if (null === $parameters['page'] || null === $parameters['page_size']) {
            $parameters = [];
        }
        $replacements = [
            'english' => [],
            'french'  => [],
        ];

        $nameFilter = $this->prophesize(NameFilter::class);
        $nameFilter->getReplacements()->willReturn($replacements);
        $nameFilter->filter(Argument::cetera())->willReturn('text');

        $stopWordFilter = $this->prophesize(StopWordFilter::class);
        $stopWordFilter->getStopWords()->willReturn($stopWords);
        $stopWordFilter->filter(Argument::cetera())->willReturn($results);

        $filter = $this->prophesize(MerchantFilterAggregate::class);
        $filter->getCategoryFilter()->willReturn($nameFilter->reveal());
        $filter->getMerchantNameFilter()->willReturn($nameFilter->reveal());
        $filter->getStopWordFilter()->willReturn($stopWordFilter->reveal());

        $httpClient = $this->prophesize(\MapleSyrupGroup\Quidco\ApiClient\Client::class);
        $httpClient->call('merchant', 'get', 'enriched', $parameters)->willReturn($response);

        $mapper = $this->prophesize(SearchableModelMapper::class);
        $mapper->getMappingProperties()->willReturn([]);
        $mapper->getLanguage()->willReturn('english');

        return new QuidcoApi($httpClient->reveal(), $mapper->reveal(), $filter->reveal());
    }
}
