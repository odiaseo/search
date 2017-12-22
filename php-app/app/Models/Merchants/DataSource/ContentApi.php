<?php

namespace MapleSyrupGroup\Search\Models\Merchants\DataSource;

use GuzzleHttp\Psr7\Response;
use MapleSyrupGroup\QCommon\Enum\DomainEnum;
use MapleSyrupGroup\QCommon\Guzzle as HttpClient;
use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantFilterAggregate;
use MapleSyrupGroup\Search\Models\Merchants\SearchableMerchantModel;
use MapleSyrupGroup\Search\Models\Merchants\SearchableModelTrait;
use MapleSyrupGroup\Search\Models\SearchableModelMapper;

/**
 * Model that represents the merchants from the content API.
 */
class ContentApi extends SearchableMerchantModel
{
    use SearchableModelTrait;

    /**
     * @var string
     */
    protected $domainUrl;

    /**
     * @var array
     */
    private $merchantFields = [
        self::FIELD_ID,
        self::FIELD_NAME,
        self::FIELD_DESCRIPTION,
        self::FIELD_STATUS,
        self::FIELD_CLICK_VALUE,
        self::FIELD_DOMAIN_ID,
        self::FIELD_URL_NAME,
        self::FIELD_IMAGES,
        self::FIELD_BEST_RATES,
        self::FIELD_BEST_RATE,
        self::FIELD_SIMILAR,
        self::FIELD_RELATED,
        self::FIELD_CATEGORIES,
        self::FIELD_OFFERS,
        self::FIELD_KEYWORDS,
        self::FIELD_EXTERNAL_REFERENCES,
        self::FIELD_TOOLBAR_OPT_OUT,
        self::FIELD_LINKS,
        self::FIELD_NAME_FILTERED,
        self::FIELD_RATES_TEXT,
        self::FIELD_RATES_TEXT_FILTERED_ACTIVE,
        self::FIELD_RATES_TEXT_FILTERED_EXPIRED,
        self::FIELD_IS_IN_STORE,
    ];

    private $dealFields = [
        self::FIELD_ID,
        self::FIELD_TITLE,
        self::FIELD_DESCRIPTION,
    ];

    private $categoryFields = [
        self::FIELD_ID,
        self::FIELD_NAME,
        self::FIELD_URL_NAME,
        self::FIELD_WEIGHT,
    ];

    /**
     * @var int|null
     */
    private $page;

    /**
     * @var int|null
     */
    private $pageSize;

    /**
     * The HTTP Client.
     *
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * ContentApiSearchableMerchantModel constructor.
     *
     * @param HttpClient              $httpClient
     * @param SearchableModelMapper   $mapper
     * @param MerchantFilterAggregate $filter
     */
    public function __construct(HttpClient $httpClient, SearchableModelMapper $mapper, MerchantFilterAggregate $filter)
    {
        $this->httpClient = $httpClient;
        $this->mapper     = $mapper;
        $this->setFilter($filter);
    }

    /**
     * @return array
     */
    protected function call()
    {
        /** @var Response $response */
        $response = $this->httpClient->getClient()->get(
            $this->getEndPointWithQuery($this->page, $this->pageSize),
            [
                'headers'         => [
                    'content-type' => 'application/json',
                ],
                'allow_redirects' => false,
            ]
        );

        $result = json_decode($response->getBody(), true);

        return [
            'result' => $result['merchants'],
            'total'  => $result['meta']['pagination']['total'],
            'pages'  => $result['meta']['pagination']['total_pages'],
        ];
    }

    /**
     * Append query parameters to the domain URL.
     *
     * @param int $page
     * @param int $pageSize
     *
     * @return string
     */
    private function getEndPointWithQuery($page, $pageSize)
    {
        $params = [
            'page'      => $page,
            'page_size' => $pageSize,
        ];

        $params = array_filter($params);
        $query  = http_build_query($params);

        if ($query) {
            return $this->getDomainUrl() . '&' . $query;
        }

        return $this->getDomainUrl();
    }

    /**
     * @param int $page
     * @param int $pageSize
     */
    protected function setPageAndSize($page, $pageSize)
    {
        $this->page     = $page;
        $this->pageSize = $pageSize;
    }

    /**
     * @param int $page     ,
     * @param int $pageSize
     *
     * @return array
     */
    public function all($page = null, $pageSize = null)
    {
        if ($page > 0 && $pageSize > 0) {
            $this->setPageAndSize($page, $pageSize);
        }

        $merchants = $this->call();

        return $merchants;
    }

    /**
     * @param array $merchant
     *
     * @return array
     */
    public function toDocumentArray($merchant)
    {
        $nested        = $this->getNestedObjects($merchant);
        $clicksValue   = (int) (isset($merchant['statistics'])) ? $merchant['statistics']['click_count_90_day'] : 0;
        $merchantName  = (string) $this->getValueFromArray($merchant, self::FIELD_NAME);
        $filter        = $this->getFilter()->getMerchantNameFilter();
        $filterOptions = ['language' => $this->mapper->getLanguage()];
        $optOut        = true === $this->getValueFromArray($merchant, self::FIELD_TOOLBAR_OPT_OUT);
        $bestRates     = (array) $this->getValueFromArray($merchant, self::FIELD_BEST_RATES, []);
        $rateText      = (array) $this->getValueFromArray($bestRates, self::FIELD_RATE_TEXT);

        $document =  [
            self::FIELD_ID                          => (int) $this->getValueFromArray($merchant, self::FIELD_ID),
            self::FIELD_NAME                        => (string) $merchantName,
            self::FIELD_NAME_FILTERED               => $filter->filter($merchantName, $filterOptions),
            self::FIELD_DESCRIPTION                 => (string) $this->encodeString(
                $this->getValueFromArray($merchant, self::FIELD_DESCRIPTION)
            ),
            self::FIELD_IS_IN_STORE                 => (int) $this->getValueFromArray($merchant, self::FIELD_IS_IN_STORE,0),
            self::FIELD_STATUS                      => (string) $this->getValueFromArray($merchant, self::FIELD_STATUS),
            self::FIELD_CLICK_VALUE                 => $clicksValue,
            self::FIELD_DOMAIN_ID                   => DomainEnum::DOMAIN_ID_SHOOP,

            // Non index/filter fields (default mapping)
            self::FIELD_URL_NAME                    => (string) $this->getValueFromArray($merchant,self::FIELD_URL_NAME),
            self::FIELD_IMAGES                      => (array) $this->getValueFromArray($nested, self::FIELD_IMAGES, []),
            self::FIELD_BEST_RATES                  => (object) $bestRates,
            self::FIELD_BEST_RATE                   => (object) $this->getValueFromArray($merchant, self::FIELD_BEST_RATE, []),
            self::FIELD_SIMILAR                     => (array) array_values(
                $this->getValueFromArray($nested, self::FIELD_SIMILAR, [])
            ),
            self::FIELD_RELATED                     => (array) array_values(
                $this->getValueFromArray($nested, self::FIELD_RELATED, [])
            ),
            self::FIELD_CATEGORIES                  => (array) $this->getValueFromArray($nested, self::FIELD_CATEGORIES, []),
            self::FIELD_OFFERS                      => (array) $this->getValueFromArray($nested, self::FIELD_DEALS, []),
            self::FIELD_KEYWORDS                    => (array) $this->getValueFromArray($nested, self::FIELD_KEYWORDS, []),
            self::FIELD_EXTERNAL_REFERENCES         => [],
            self::FIELD_TOOLBAR_OPT_OUT             => $optOut,
            self::FIELD_LINKS                       => $this->extractMerchantLinks($merchant),
            self::FIELD_RATES_TEXT                  => (array) $this->filterRatesText($rateText),
            self::FIELD_EXTERNAL_REFERENCES         => [],

            //rates textx
            self::FIELD_RATES_TEXT_FILTERED_ACTIVE  => $rateText,
            self::FIELD_RATES_TEXT_FILTERED_EXPIRED => [],
        ];

        return $this->addCashbackSortFields($document, $merchant);
    }

    /**
     * @param array $merchant
     *
     * @return array
     */
    protected function getNestedObjects($merchant)
    {
        $nested = [
            self::FIELD_CATEGORIES => $this->getNestedCategories($merchant),
            self::FIELD_DEALS      => $this->getNestedDeals($merchant),
            self::FIELD_IMAGES     => $this->getNestedImages($merchant),
            self::FIELD_KEYWORDS   => $this->getFlattenedKeywordArray($merchant),
        ];

        if (!empty($merchant[self::FIELD_RELATED_MERCHANT_RATES][0])) {
            $nested = array_merge($nested, $merchant[self::FIELD_RELATED_MERCHANT_RATES][0]);
        }

        return $nested;
    }

    /**
     * Flatten keywords.
     *
     * @param $merchant
     *
     * @return array
     */
    private function getFlattenedKeywordArray($merchant)
    {
        $keywords = [];

        if (empty($merchant[self::FIELD_KEYWORDS])) {
            return $keywords;
        }

        foreach ($merchant[self::FIELD_KEYWORDS] as $keyword) {
            $keywords[] = $this->getValueFromArray($keyword, self::FIELD_KEYWORD);
        }

        return $keywords;
    }

    /**
     * List nested deals.
     *
     * @param $merchantData
     *
     * @return array
     */
    private function getNestedDeals($merchantData)
    {
        $nestedDeals = [];

        if (empty($merchantData[self::FIELD_LIVE_DEALS])) {
            return $nestedDeals;
        }

        foreach ($merchantData[self::FIELD_LIVE_DEALS] as $offer) {
            $array         = [
                self::FIELD_ID          => (int) $this->getValueFromArray($offer, self::FIELD_ID),
                self::FIELD_TITLE       => (string) $this->getValueFromArray($offer, self::FIELD_TITLE),
                self::FIELD_DESCRIPTION => (string) $this->getValueFromArray($offer, self::FIELD_DESCRIPTION),
            ];
            $nestedDeals[] = (object) $array;
        }

        return $nestedDeals;
    }

    /**
     * List nested categories.
     *
     * @param $merchant
     *
     * @return array
     */
    private function getNestedCategories($merchant)
    {
        $nested = [];

        if (empty($merchant[self::FIELD_CATEGORIES])) {
            return $nested;
        }

        $filterOption = ['language' => $this->mapper->getLanguage()];
        foreach ($merchant[self::FIELD_CATEGORIES] as $category) {
            $name     = (string) $this->getValueFromArray($category, self::FIELD_NAME);
            $array    = [
                self::FIELD_ID       => (int) $this->getValueFromArray($category, self::FIELD_ID),
                self::FIELD_NAME     => $this->getFilter()->getCategoryFilter()->filter($name, $filterOption),
                self::FIELD_URL_NAME => (string) $this->getValueFromArray($category, self::FIELD_URL_NAME),
                self::FIELD_WEIGHT   => $this->getCategoryWeightingByName($merchant),
            ];
            $nested[] = (object) $array;
        }

        return $nested;
    }

    /**
     * Get API endpoint domain from configuration file if not specified.
     *
     * @@return string
     */
    private function getDomainUrl()
    {
        if (empty($this->domainUrl)) {
            $this->domainUrl = config('qplatform.api.endpoints.content_get_merchant_details');
        }

        return $this->domainUrl;
    }

    /**
     * @return array
     */
    public function getMerchantFields()
    {
        return $this->merchantFields;
    }

    /**
     * @return array
     */
    public function getCategoryFields()
    {
        return $this->categoryFields;
    }

    /**
     * @return array
     */
    public function getDealFields()
    {
        return $this->dealFields;
    }

    /**
     * @param array $merchant
     *
     * @return array
     */
    private function extractMerchantLinks(array $merchant)
    {
        $links = $this->getValueFromArray($merchant, 'links', []);

        return array_map(function ($link) {
            return $this->getValueFromArray($link, 'url');
        }, $links);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCategoryWeightingByName($merchant)
    {
        $popularity = (int) (isset($merchant['statistics'])) ? $merchant['statistics']['click_count_90_day'] : 0;

        return 1 * $popularity;
    }
}
