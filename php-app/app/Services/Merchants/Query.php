<?php

namespace MapleSyrupGroup\Search\Services\Merchants;

use InvalidArgumentException;
use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;

class Query implements DomainQuery
{
    use QueryFilterTrait;

    const LANGUAGE_FRENCH   = 'french';
    const LANGUAGE_ENGLISH  = 'english';
    const MAXIMUM_PAGE_SIZE = 40;
    const FIELD_IS_IN_STORE = 'is_in_store';

    /**
     * @var string
     */
    private $term;

    /**
     * @var int
     */
    private $domainId;

    /**
     * @var string
     */
    private $language = self::LANGUAGE_ENGLISH;

    /**
     * @var int
     */
    private $page = 1;

    /**
     * @var int
     */
    private $pageSize = self::MAXIMUM_PAGE_SIZE;

    /**
     * IDs of merchants to be filtered out from the search results.
     *
     * @var array
     */
    private $excludedMerchants = [];

    /**
     * Indicates if debug mode is enabled or not.
     *
     * @var bool
     */
    private $debug = false;

    /**
     * @var array
     */
    private $strategy = null;

    /**
     * IDs of categories to limit result to if there are multiple categories with the same name.
     *
     * @var array
     */
    private $categoryIds = [];

    /**
     * @var SortParameter
     */
    protected $sortParameter;

    /**
     * @param string        $term
     * @param int           $domainId
     * @param SortParameter $sortParameter
     */
    public function __construct($term, $domainId, SortParameter $sortParameter)
    {
        $this->term          = $term;
        $this->domainId      = (int) $domainId;
        $this->sortParameter = $sortParameter;
    }

    /**
     * @return array
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * @param mixed $strategy
     */
    public function setStrategy($strategy)
    {
        if (null !== $strategy) {
            $this->strategy = (array) $strategy;
        }
    }

    /**
     * Set the language we are querying in.
     *
     * Valid values are "english" and "french"
     *
     * @param string $language
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $languages = [self::LANGUAGE_ENGLISH, self::LANGUAGE_FRENCH];
        if (in_array($language, $languages)) {
            $this->language = $language;
        } elseif ($language !== null) {
            $message = 'Expected language to be ' . json_encode($languages);
            $message .= ' was : ' . $language;

            throw new InvalidArgumentException($message, ExceptionCodes::CODE_INVALID_ARGUMENT);
        }

        return $this;
    }

    /**
     * @param int $page
     *
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = (int) $page;

        return $this;
    }

    /**
     * @param int $pageSize
     *
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $pageSize = (int) $pageSize;
        if ($pageSize > self::MAXIMUM_PAGE_SIZE) {
            $pageSize = self::MAXIMUM_PAGE_SIZE;
        }
        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * @return string
     */
    public function getSearchTerm()
    {
        return $this->term;
    }

    /**
     * @return int
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * Cleanup  merchant IDs retrieved from query parameter
     * Ensure the value is a zero indexed array to avoid conversion to object when json encoded.
     *
     * @param string $excludedMerchants
     */
    public function setExcludedMerchants($excludedMerchants)
    {
        if (is_string($excludedMerchants)) {
            $excludedMerchants = explode(',', strip_tags($excludedMerchants));
        }

        $excludedMerchants = array_filter(
            (array) $excludedMerchants,
            function ($value) {
                return is_numeric($value) && !empty($value * 1);
            }
        );

        $this->excludedMerchants = array_values(array_unique($excludedMerchants));
    }

    /**
     * @return array
     */
    public function getExcludedMerchants()
    {
        return $this->excludedMerchants;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = (bool) $debug;
    }

    /**
     * @return array
     */
    public function getCategoryIds()
    {
        return $this->categoryIds;
    }

    /**
     * @param array $categoryIds
     */
    public function setCategoryIds($categoryIds)
    {
        $this->categoryIds = array_filter((array) $categoryIds);
    }

    /**
     * @return array
     */
    public function getSortOrder()
    {
        return $this->sortParameter->getSortOrder();
    }

    /**
     * @return string
     */
    public function getSortField()
    {
        return $this->sortParameter->getSortField();
    }

    /**
     * @return string
     */
    public function getSortDirection()
    {
        return $this->sortParameter->getSortDirection();
    }
}
