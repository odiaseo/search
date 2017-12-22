<?php

namespace MapleSyrupGroup\Search\Models\Merchants\DataSource;

use MapleSyrupGroup\QCommon\Guzzle as HttpClient;
use MapleSyrupGroup\Search\Models\Merchants\Filters\CategoryNameFilter;
use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantFilterAggregate;
use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantNameFilter;
use MapleSyrupGroup\Search\Models\Merchants\Filters\StopWordFilter;
use MapleSyrupGroup\Search\Models\SearchableModelMapper;
use MapleSyrupGroup\Search\TestCase;
use Psr\Http\Message\MessageInterface;

/**
 * Model that represents the merchants from the content API.
 */
class ContentApiTest extends TestCase
{
    /**
     * @group model
     */
    public function testThatModelCanRetrieveDomainFromConfig()
    {
        config(['qplatform.api.endpoints.content_get_merchant_details' => 'http:://www.test.com']);
        $this->assertInstanceOf(ContentApi::class, $this->getModel('', [], [], 1));
    }

    /**
     * @group model
     * @dataProvider pageParametersProvider
     */
    public function testThatTheContentApiReturnsPaginatedSearchResult($page, $pageSize, $totalResult, $numberOfPages)
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

        $domain   = 'http:://www.test.com';
        $model    = $this->getModel($domain, $params, $totalResult, $numberOfPages);
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
        $model    = $this->getModel('http://www.test.com', [], 1, 1);
        $document = $model->toDocumentArray($merchant);

        foreach ($model->getMerchantFields() as $field) {
            $this->assertArrayHasKey($field, $document);
        }

        foreach ($document[$model::FIELD_CATEGORIES] as $category) {
            $this->assertInstanceOf(\stdClass::class, $category);
            foreach ($model->getCategoryFields() as $field) {
                $this->assertArrayHasKey($field, (array)$category);
            }
        }

        foreach ($document[$model::FIELD_IMAGES] as $image) {
            $this->assertInstanceOf(\stdClass::class, $image);
            foreach ($model->getImageFields() as $field) {
                $this->assertArrayHasKey($field, (array)$image);
            }
        }

        foreach ($document[$model::FIELD_OFFERS] as $deal) {
            $this->assertInstanceOf(\stdClass::class, $deal);
            foreach ($model->getDealFields() as $field) {
                $this->assertArrayHasKey($field, (array)$deal);
            }
        }
    }

    public function merchantDataProvider()
    {
        return [
            [
                [
                    'images' => [
                        [
                            ContentApi::FIELD_WIDTH  => 300,
                            ContentApi::FIELD_HEIGHT => 250,
                        ],
                    ],
                ],
            ],
            [
                [
                    'categories' => [
                        [
                            'id'       => 4,
                            'name'     => 'Fashion',
                            'url_name' => 'category',
                        ],
                    ],
                    'images'     => [

                    ],

                    'live_deals' => [
                        [
                            'id'   => 23,
                            'name' => 'Marks &nbsp; Spencer Shoes',
                        ],
                    ],
                    'keywords'   => [
                        ['keyword' => 'shirts'],
                        ['keyword' => 'food'],
                    ],
                    'links'      => [
                        [
                            'url' => 'www.argos.co.uk',
                        ],
                    ],
                ],
            ],
            [
                [
                    'id'                     => 2,
                    'description'            => ' <div>   Marks &nbsp;   Spencers &pound; <br /></div>',
                    'related_merchant_rates' => [
                        ['best_match' => 23.50],
                        ['next' => 89.45],
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

    private function getModel($domain, $params, $total, $numPages)
    {
        $calledDomain = $domain;

        if ($params = array_filter($params)) {
            $calledDomain .= '&' . http_build_query($params);
        }

        $response = $this->prophesize(MessageInterface::class);
        $body     = json_encode(
            [
                'merchants' => [],
                'meta'      => [
                    'pagination' => [
                        'total'       => $total,
                        'total_pages' => $numPages,
                    ],
                ],
            ]
        );

        $response->getBody()->willReturn($body);

        $httpClient = $this->prophesize(\GuzzleHttp\Client::class);
        $httpClient->get(
            $calledDomain,
            [
                'headers'         => [
                    'content-type' => 'application/json',
                ],
                'allow_redirects' => false,
            ]
        )->willReturn($response);

        $client = $this->prophesize(HttpClient::class);
        $client->getClient()->willReturn($httpClient->reveal());

        $mapper = $this->prophesize(SearchableModelMapper::class);
        $mapper->getMappingProperties()->willReturn([]);
        $mapper->getLanguage()->willReturn('french');

        if ($domain) {
            config(['qplatform.api.endpoints.content_get_merchant_details' => $domain]);
        }

        $replacements = [
            'english' => [],
            'french'  => [],
        ];

        $stopWordFilter     = new StopWordFilter([]);
        $categoryNameFilter = new CategoryNameFilter($replacements);
        $mFilter            = new MerchantNameFilter($replacements);
        $filter             = new MerchantFilterAggregate($stopWordFilter, $categoryNameFilter, $mFilter);

        return new ContentApi($client->reveal(), $mapper->reveal(), $filter);
    }

    public function tearDown()
    {
        restore_error_handler();
    }
}
