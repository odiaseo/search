<?php

namespace MapleSyrupGroup\Search\Models\Merchants;

use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantFilterAggregate;
use MapleSyrupGroup\Search\Models\SearchableModel;
use MapleSyrupGroup\Search\Models\SearchableModelMapper;

/**
 * Definition for the mapping for a merchant.
 *
 * This ensures regardless of the source, our merchants can use the same query
 */
abstract class SearchableMerchantModel implements SearchableModel
{
    const FIELD_ID                          = 'id';
    const FIELD_NAME                        = 'name';
    const FIELD_NAME_FILTERED               = 'name_filtered';
    const FIELD_DESCRIPTION                 = 'description';
    const FIELD_STATUS                      = 'status';
    const FIELD_CLICK_VALUE                 = 'clicks_value';
    const FIELD_DOMAIN_ID                   = 'domain_id';
    const FIELD_URL_NAME                    = 'url_name';
    const FIELD_IMAGES                      = 'images';
    const FIELD_BEST_RATES                  = 'best_rates';
    const FIELD_BEST_RATE                   = 'best_rate';
    const FIELD_RATES_TEXT                  = 'rates_text';
    const FIELD_RATE_TEXT                   = 'rate_text';
    const FIELD_RATES_TEXT_FILTERED_ACTIVE  = 'rates_text_filtered_active';
    const FIELD_RATES_TEXT_FILTERED_EXPIRED = 'rates_text_filtered_expired';
    const FIELD_SIMILAR                     = 'similar';
    const FIELD_RELATED                     = 'related';
    const FIELD_CATEGORIES                  = 'categories';
    const FIELD_OFFERS                      = 'offers';
    const FIELD_KEYWORDS                    = 'keywords';
    const FIELD_KEYWORD                     = 'keyword';
    const FIELD_EXTERNAL_REFERENCES         = 'external_references';
    const FIELD_TOOLBAR_OPT_OUT             = 'toolbar_opt_out';
    const FIELD_IS_IN_STORE                 = 'is_in_store';
    const FIELD_LINKS                       = 'links';
    const FIELD_DEALS                       = 'deals';
    const FIELD_RELATED_MERCHANT_RATES      = 'related_merchant_rates';
    const FIELD_MERCHANT_ID                 = 'merchant_id';
    const FIELD_TITLE                       = 'title';
    const FIELD_LIVE_DEALS                  = 'live_deals';
    const FIELD_VARIANT                     = 'variant';
    const FIELD_TYPE_ID                     = 'type_id';
    const FIELD_TYPE_NAME                   = 'type_name';
    const FIELD_WIDTH                       = 'width';
    const FIELD_HEIGHT                      = 'height';
    const FIELD_PATH                        = 'path';
    const FIELD_TYPE                        = 'type';
    const FIELD_RELATED_MERCHANT_ID         = 'related_merchant_id';
    const FIELD_SIMILAR_MERCHANT_ID         = 'similar_merchant_id';
    const FIELD_SYNONYMS                    = 'synonyms';
    const FIELD_BECOME_ID                   = 'become_id';
    const FIELD_WEIGHT                      = 'weight';
    const FIELD_CASHBACK_AMOUNT             = 'cashback_amount';
    const FIELD_CASHBACK_PERCENTAGE         = 'cashback_percentage';

    /**
     * Searchable Model Mapping.
     *
     * @var SearchableModelMapper
     */
    protected $mapper;

    /**
     * @var array
     */
    protected $imageFields = [
        self::FIELD_MERCHANT_ID,
        self::FIELD_VARIANT,
        self::FIELD_TYPE_ID,
        self::FIELD_TYPE_NAME,
        self::FIELD_WIDTH,
        self::FIELD_HEIGHT,
        self::FIELD_PATH,
    ];

    /**
     * @var MerchantFilterAggregate
     */
    protected $filter;

    /**
     * @return array
     */
    public function getMappingProperties()
    {
        return $this->mapper->getMappingProperties();
    }

    /**
     * List nested images.
     *
     * @param $merchant
     *
     * @return array[]
     */
    public function getNestedImages($merchant)
    {
        $nested    = [];
        $imageData = $this->getValueFromArray($merchant, self::FIELD_IMAGES, []);

        foreach ($imageData as $image) {
            $array    = [
                self::FIELD_MERCHANT_ID => (int) $this->getValueFromArray($image, self::FIELD_MERCHANT_ID),
                self::FIELD_VARIANT     => (int) $this->getValueFromArray($image, self::FIELD_VARIANT),
                self::FIELD_TYPE_ID     => (int) $this->getValueFromArray($image, self::FIELD_TYPE_ID),
                self::FIELD_TYPE_NAME   => (string) $this->getValueFromArray($image, self::FIELD_TYPE_NAME),
                self::FIELD_WIDTH       => (int) $this->getValueFromArray($image, self::FIELD_WIDTH),
                self::FIELD_HEIGHT      => (int) $this->getValueFromArray($image, self::FIELD_HEIGHT),
                self::FIELD_PATH        => (string) $this->getValueFromArray($image, self::FIELD_PATH),
            ];
            $nested[] = (object) $array;
        }

        return $nested;
    }

    /**
     * @param array $document
     * @param array $merchant
     *
     * @return array
     */
    protected function addCashbackSortFields(array $document, array $merchant)
    {
        $bestRate = (array) $this->getValueFromArray($merchant, self::FIELD_BEST_RATE, []);
        $type  = $this->getValueFromArray($bestRate, 'cashback_type', 'currency');
        $value = $this->getValueFromArray($bestRate, 'cashback_value', 0);

        if ($type == 'currency') {
            $document[self::FIELD_CASHBACK_AMOUNT]     = $value;
        } else {
            $document[self::FIELD_CASHBACK_PERCENTAGE] = $value;
        }

        return $document;
    }

    /**
     * Remove stop words, spaces and prices from the rates texts.
     *
     * @param array $data
     *
     * @return array
     */
    protected function filterRatesText(array $data)
    {
        $params = ['language' => $this->mapper->getLanguage(), 'field' => self::FIELD_RATES_TEXT];
        $data   = $this->getFilter()->getStopWordFilter()->filter($data, $params);

        $data = array_filter($data, function ($value) {
            return !(empty($value) || is_numeric($value));
        });

        asort($data);

        return array_values(array_unique($data));
    }

    /**
     * Get value from the array.
     *
     * @return mixed|string
     *
     * @param array  $data
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getValueFromArray(array $data, $key, $default = '')
    {
        if (array_key_exists($key, $data)) {
            return $data[$key];
        }

        return $default;
    }

    /**
     * @return array
     */
    public function getImageFields()
    {
        return $this->imageFields;
    }

    /**
     * @return MerchantFilterAggregate
     */
    protected function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param MerchantFilterAggregate $merchantFilter
     *
     * @return $this
     */
    protected function setFilter($merchantFilter)
    {
        $this->filter = $merchantFilter;

        return $this;
    }

    /**
     * Get fields required for merchant results.
     *
     * @return array
     */
    abstract public function getMerchantFields();

    /**
     * @param $merchant
     *
     * @return int
     */
    abstract protected function getCategoryWeightingByName($merchant);
}
