<?php

namespace MapleSyrupGroup\Search\Services\Merchants;

class SortParameter
{
    const MAP_FIELD_SCORE           = '_score';
    const MAP_FIELD_CLICKS_VALUE    = 'clicks_value';
    const FIELD_POPULARITY          = 'popularity';
    const FIELD_CASHBACK            = 'cashback';
    const FIELD_CASHBACK_AMOUNT     = 'cashback_amount';
    const FIELD_CASHBACK_PERCENTAGE = 'cashback_percentage';
    const FIELD_RELEVANCE           = 'relevance';

    /**
     * Map domain fields to elastic field.
     *
     * @var array
     */
    public static $allowedFields = [
        self::FIELD_RELEVANCE           => self::MAP_FIELD_SCORE,
        self::FIELD_POPULARITY          => self::MAP_FIELD_CLICKS_VALUE,
        self::FIELD_CASHBACK_AMOUNT     => self::FIELD_CASHBACK_AMOUNT,
        self::FIELD_CASHBACK_PERCENTAGE => self::FIELD_CASHBACK_PERCENTAGE,
    ];

    /**
     * @var array
     */
    private $sortOrder = [];

    /**
     * @var string
     */
    private $sortField = '';

    /**
     * @var string
     */
    private $sortDirection = '';

    /**
     * SortParameter constructor.
     *
     * @param string $field
     * @param string $direction
     */
    public function __construct($field = '', $direction = '')
    {
        $this->setParameters($field, $direction);
    }

    /**
     * @return string
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * @return string
     */
    public function getSortDirection()
    {
        return $this->sortDirection;
    }

    /**
     * @return array
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Both values should either be set or both empty.
     *
     * @param string $field
     * @param string $direction
     *
     * @return $this
     */
    private function setParameters($field, $direction)
    {
        $this->sortField     = trim(strtolower($field));
        $this->sortDirection = trim(strtolower($direction));

        $this->validateSortParameters($this->sortField, $this->sortDirection);

        if ($this->sortField) {
            $this->sortOrder = [self::$allowedFields[$this->sortField] => $this->sortDirection];
        }

        return $this;
    }

    /**
     * @param string $field
     * @param string $direction
     */
    private function validateSortParameters($field, $direction)
    {
        if ($field xor $direction) {
            $message = sprintf(
                'Invalid parameters found: [%s => %s]. Valid sort field and direction must be provided',
                $field,
                $direction
            );
            throw new InvalidSortParameterException($message);
        }

        if ($field && !array_key_exists($field, self::$allowedFields)) {
            throw new InvalidSortParameterException(sprintf('Invalid field: %s', $field));
        }
    }
}
