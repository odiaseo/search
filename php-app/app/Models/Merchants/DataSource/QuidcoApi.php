<?php

namespace MapleSyrupGroup\Search\Models\Merchants\DataSource;

use MapleSyrupGroup\QCommon\Enum\DomainEnum;
use MapleSyrupGroup\Quidco\ApiClient\ClientInterface;
use MapleSyrupGroup\Quidco\ApiClient\Exception\ApiErrorException;
use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;
use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantFilterAggregate;
use MapleSyrupGroup\Search\Models\Merchants\SearchableMerchantModel;
use MapleSyrupGroup\Search\Models\Merchants\SearchableModelTrait;
use MapleSyrupGroup\Search\Models\SearchableModelMapper as MapperModel;

/**
 * Model for Merchants originally from Quidco's system.
 */
class QuidcoApi extends SearchableMerchantModel
{
    use SearchableModelTrait;

    const PAGE      = 'page';
    const PAGE_SIZE = 'page_size';
    const TOTAL     = 'total';
    const PAGES     = 'pages';
    const RESULT    = 'result';

    /**
     * @var array
     */
    private $merchantFields = [
        self::FIELD_ID,
        self::FIELD_NAME,
        self::FIELD_NAME_FILTERED,
        self::FIELD_DESCRIPTION,
        self::FIELD_STATUS,
        self::FIELD_CLICK_VALUE,
        self::FIELD_DOMAIN_ID,
        self::FIELD_URL_NAME,
        self::FIELD_IMAGES,
        self::FIELD_BEST_RATES,
        self::FIELD_BEST_RATE,
        self::FIELD_RATES_TEXT,
        self::FIELD_RATES_TEXT_FILTERED_ACTIVE,
        self::FIELD_RATES_TEXT_FILTERED_EXPIRED,
        self::FIELD_SIMILAR,
        self::FIELD_RELATED,
        self::FIELD_CATEGORIES,
        self::FIELD_KEYWORDS,
        self::FIELD_EXTERNAL_REFERENCES,
        self::FIELD_IS_IN_STORE,
    ];

    private $resultKeys = [
        'merchants',
        'meta',
    ];

    /**
     * The HTTP Client.
     *
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * QuidcoApiSearchableMerchantModel constructor.
     *
     * @param ClientInterface         $httpClient
     * @param MapperModel             $mapper
     * @param MerchantFilterAggregate $filter
     */
    public function __construct(ClientInterface $httpClient, MapperModel $mapper, MerchantFilterAggregate $filter)
    {
        $this->httpClient = $httpClient;
        $this->mapper     = $mapper;
        $this->setFilter($filter);
    }

    /**
     * Get all of the merchants with enough detail in them to build the search index.
     *
     * This makes an API Call to Quidco's  merchants get enriched method.
     *
     * @param int $page     The page.
     * @param int $pageSize The page size.
     *
     * @return array
     *
     * @throws \Exception
     */
    public function all($page = 1, $pageSize = 100)
    {
        $merchants = $this->call($page, $pageSize);

        return $merchants;
    }

    /**
     * Call the Quidco API and retrieve the merchants.
     *
     * @param int|null $page
     * @param int|null $pageSize
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function call($page, $pageSize)
    {
        $params   = $this->getRequestParameters($page, $pageSize);
        $response = $this->httpClient->call('merchant', 'get', 'enriched', $params);
        $this->validateResponse($response);

        return [
            self::RESULT => json_decode(json_encode($response->merchants), true),
            self::TOTAL  => $response->meta->pagination->total,
            self::PAGES  => $response->meta->pagination->total_pages,
        ];
    }

    /**
     * @param $response
     */
    private function validateResponse($response)
    {
        foreach ($this->resultKeys as $property) {
            if (!isset($response->$property)) {
                throw new ApiErrorException(
                    "Expected property $property does not exist, " . json_encode($response),
                    ExceptionCodes::CODE_UNEXPECTED_API_ERROR
                );
            }
        }
    }

    /**
     * @param int $page
     * @param int $pageSize
     *
     * @return array
     */
    private function getRequestParameters($page, $pageSize)
    {
        $parameters = [];

        if (null !== $page && null !== $pageSize) {
            $parameters = [
                self::PAGE      => $page,
                self::PAGE_SIZE => $pageSize,
            ];
        }

        return $parameters;
    }

    /**
     * Convert a merchant to something we can insert into elastic search.
     *
     * @param array $merchant The merchant from the API.
     *
     * @return array
     */
    public function toDocumentArray($merchant)
    {
        $merchant      = (array) $merchant;
        $nested        = $this->getNestedObjects($merchant);
        $merchantName  = (string) $this->getValueFromArray($merchant, self::FIELD_NAME);
        $filterOptions = ['language' => $this->mapper->getLanguage()];

        $document = [
            self::FIELD_ID            => (int) $this->getValueFromArray($merchant, self::FIELD_ID),
            self::FIELD_NAME          => $merchantName,
            self::FIELD_NAME_FILTERED => $this->getFilter()->getMerchantNameFilter()
                ->filter($merchantName, $filterOptions),
            self::FIELD_DESCRIPTION   => (string) $this->encodeString(
                $this->getValueFromArray($merchant, self::FIELD_DESCRIPTION)
            ),
            self::FIELD_STATUS        => (string) $this->getValueFromArray($merchant, self::FIELD_STATUS),
            self::FIELD_CLICK_VALUE   => (int) $this->getValueFromArray($merchant, self::FIELD_CLICK_VALUE),
            self::FIELD_DOMAIN_ID     => DomainEnum::DOMAIN_ID_QUIDCO,

            // Non index/filter fields (default mapping)
            self::FIELD_URL_NAME      => (string) $this->getValueFromArray($merchant, self::FIELD_URL_NAME),
            self::FIELD_IMAGES        => (array) $this->getValueFromArray($nested, self::FIELD_IMAGES, []),
            self::FIELD_BEST_RATES    => (object) $this->getValueFromArray($merchant, self::FIELD_BEST_RATES, []),
            self::FIELD_BEST_RATE     => (object) $this->getValueFromArray($merchant, self::FIELD_BEST_RATE, []),
            self::FIELD_RATES_TEXT    => (array) $this->getValueFromArray($merchant, self::FIELD_RATES_TEXT),

            self::FIELD_RATES_TEXT_FILTERED_ACTIVE => $this->filterRatesText(
                (array) $this->getValueFromArray($merchant, self::FIELD_RATES_TEXT_FILTERED_ACTIVE, [])
            ),

            self::FIELD_RATES_TEXT_FILTERED_EXPIRED => $this->filterRatesText(
                (array) $this->getValueFromArray($merchant, self::FIELD_RATES_TEXT_FILTERED_EXPIRED, [])
            ),

            self::FIELD_IS_IN_STORE => (int) $this->getValueFromArray($merchant, self::FIELD_IS_IN_STORE, 0),
            self::FIELD_SIMILAR     => (array) array_values(
                $this->getValueFromArray($nested, self::FIELD_SIMILAR, [])
            ),
            self::FIELD_RELATED     => (array) array_values(
                $this->getValueFromArray($nested, self::FIELD_RELATED, [])
            ),

            self::FIELD_CATEGORIES => (array) $this->getValueFromArray($nested, self::FIELD_CATEGORIES, []),
            self::FIELD_KEYWORDS   => (array) array_values(
                $this->getValueFromArray($merchant, self::FIELD_KEYWORDS, [])
            ),

            self::FIELD_EXTERNAL_REFERENCES => [
                [
                    'service' => 'connexity',
                    'type'    => 'become_merchant_id',
                    'ref'     => (string) $this->getValueFromArray($merchant, self::FIELD_BECOME_ID),
                ],
            ],
        ];

        return $this->addCashbackSortFields($document, $merchant);
    }

    /**
     * Convert objects into the correct type for Elastic Search.
     *
     * @param array $merchant
     *
     * @return array
     */
    protected function getNestedObjects($merchant)
    {
        return [
            self::FIELD_CATEGORIES => $this->getNestedCategories($merchant),
            self::FIELD_IMAGES     => $this->getNestedImages($merchant),
            self::FIELD_SIMILAR    => $this->getSimilarMerchants($merchant),
            self::FIELD_RELATED    => $this->getRelatedMerchants($merchant),
        ];
    }

    /**
     * List nested categories.
     *
     * Convert category titles to lower case so that case insensitive exact matches can be performed
     * on a not_analyzed index
     *
     * @param $merchant
     *
     * @return array
     */
    private function getNestedCategories($merchant)
    {
        $nested       = [];
        $categoryData = $this->getValueFromArray($merchant, self::FIELD_CATEGORIES, []);

        foreach ($categoryData as $category) {
            $name     = $this->getValueFromArray($category, self::FIELD_NAME);
            $nested   = $this->addCategoryToList($nested, $category, $merchant, $name);
            $synonyms = (array) $this->getValueFromArray($category, self::FIELD_SYNONYMS, []);

            array_walk($synonyms, function ($name) use (&$nested, $merchant) {
                $nested = $this->addCategoryToList($nested, [], $merchant, $name);
            });
        }

        asort($nested);

        return array_values($nested);
    }

    /**
     * @param array  $nested
     * @param array  $category
     * @param array  $merchant
     * @param string $name
     *
     * @return mixed
     */
    private function addCategoryToList($nested, $category, $merchant, $name)
    {
        $name         = trim(strtolower((string) $name));
        $filterOption = ['language' => $this->mapper->getLanguage()];

        if (empty($nested[$name])) {
            $nested[$name] = (object) [
                self::FIELD_ID       => $this->getValueFromArray($category, self::FIELD_ID, 0),
                self::FIELD_NAME     => $this->getFilter()->getCategoryFilter()->filter($name, $filterOption),
                self::FIELD_WEIGHT   => $this->getCategoryWeightingByName($merchant),
                self::FIELD_URL_NAME => (string) $this->getValueFromArray($category, self::FIELD_URL_NAME, ''),
            ];
        }

        return $nested;
    }

    /**
     * Method to return category weighting for merchants which would be a function of the merchant's popularity.
     * Weight is set to 1 until a method to determine the category's weight is agreed and implemented.
     *
     * @param array $merchant
     *
     * @return int
     */
    protected function getCategoryWeightingByName($merchant)
    {
        $popularity = $this->getValueFromArray($merchant, self::FIELD_CLICK_VALUE);

        return (int) $popularity;
    }

    /**
     * Get related merchants from the search results.
     *
     * @param $merchantList
     *
     * @return array
     */
    private function getRelatedMerchants($merchantList)
    {
        return $this->getMerchantsByRelationship($merchantList, self::FIELD_RELATED, self::FIELD_RELATED_MERCHANT_ID);
    }

    /**
     * Get similar merchants from the serach results.
     *
     * @param $merchantList
     *
     * @return array
     */
    private function getSimilarMerchants($merchantList)
    {
        return $this->getMerchantsByRelationship($merchantList, self::FIELD_SIMILAR, self::FIELD_SIMILAR_MERCHANT_ID);
    }

    /**
     * Get merchant by relationship from resultset.
     *
     * @param array  $merchantList
     * @param string $relation
     * @param string $idField
     *
     * @return array
     */
    private function getMerchantsByRelationship($merchantList, $relation, $idField)
    {
        $nested = [];
        $data   = $this->getValueFromArray($merchantList, $relation, []);

        foreach ($data as $merchant) {
            $bestRate = $this->getBestRateFromArray($merchant);
            $array    = [
                self::FIELD_MERCHANT_ID         => (int) $this->getValueFromArray($merchant, self::FIELD_MERCHANT_ID),
                self::FIELD_RELATED_MERCHANT_ID => (int) $this->getValueFromArray($merchant, $idField),
                self::FIELD_NAME                => (string) $this->getValueFromArray($merchant, self::FIELD_NAME),
                self::FIELD_TYPE                => (string) $this->getValueFromArray($merchant, self::FIELD_TYPE),
                self::FIELD_URL_NAME            => (string) $this->getValueFromArray($merchant, self::FIELD_URL_NAME),
                self::FIELD_IS_IN_STORE         => (int) $this->getValueFromArray($merchant, self::FIELD_IS_IN_STORE),
                self::FIELD_BEST_RATES          => $bestRate,
                self::FIELD_BEST_RATE           => $bestRate,
            ];

            $nested[] = (object) $array;
        }

        return $nested;
    }

    private function getBestRateFromArray($merchant)
    {
        $bestRate = $this->getValueFromArray($merchant, self::FIELD_BEST_RATE, []);
        if (empty($bestRate)) {
            return [];
        }

        return $bestRate;
    }

    /**
     * @return array
     */
    public function getMerchantFields()
    {
        return $this->merchantFields;
    }
}
