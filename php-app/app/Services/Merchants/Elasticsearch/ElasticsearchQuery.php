<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch;

use MapleSyrupGroup\Search\Models\Merchants\WordFilter;
use MapleSyrupGroup\Search\Services\Merchants\Query;

abstract class ElasticsearchQuery
{
    /**
     * Characters we can't send to ElasticSearch without changing the behaviour of the query (unless they are escaped).
     * Brackets removed because they are used in merchant names e.g. Halfords (In-store).
     */
    const ES_RESERVED_CHARS = '\ / + - = && || > < ! { } [ ] ^ " ~ * ? :';

    const DOMAIN_ID_FIELD = 'domain_id';
    const DIR_ASC = 'asc';
    const DIR_DESC = 'desc';

    /**
     * @var WordFilter
     */
    protected static $nameFilter;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var string
     */
    protected $searchTerm;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var int
     */
    protected $pageSize = 40;

    /**
     * @var int
     */
    protected $page = 1;

    /**
     * IDs of merchants to be filtered out from the search results.
     *
     * @var array
     */
    protected $excludedMerchants = [];

    /**
     * IDs of categories to limit results to.
     *
     * @var array
     */
    protected $categoryIds = [];

    /**
     * @var array
     */
    protected $sortOrder = [];

    /**
     * @var array
     */
    protected $defaultSortOrder = [];

    /**
     * @var bool
     */
    protected $explain = false;

    /**
     * @var array
     */
    protected static $reservedArrayList = [];

    /**
     * @var string
     */
    protected $rawQuery;

    /**
     * ElasticsearchQuery constructor.
     *
     * @param string $searchTerm
     * @param WordFilter|null $filter
     */
    protected function __construct($searchTerm, WordFilter $filter = null)
    {
        $this->setSearchTerm($searchTerm);
        self::setNameFilter($filter);
    }

    /**
     * @param Query $query
     * @param WordFilter $filter
     *
     * @return ElasticsearchQuery
     */
    public static function fromQuery(Query $query, WordFilter $filter = null)
    {
        $esQuery                                 = new static($query->getSearchTerm(), $filter);
        $esQuery->language                       = $query->getLanguage();
        $esQuery->page                           = $query->getPage();
        $esQuery->pageSize                       = $query->getPageSize();
        $esQuery->filters[self::DOMAIN_ID_FIELD] = $query->getDomainId();
        $esQuery->excludedMerchants              = $query->getExcludedMerchants();
        $esQuery->categoryIds                    = $query->getCategoryIds();
        $esQuery->explain                        = $query->isDebug();

        foreach ($query->getSortOrder() as $field => $direction) {
            $esQuery->sortOrder[$field] = ['order' => $esQuery->validateSortDirection($direction)];
        }

        foreach ($query->getFilters() as $field => $value) {
            $esQuery->filters[$field] = $value;
        }

        return $esQuery;
    }

    /**
     * @param string $direction
     *
     * @return string
     */
    protected function validateSortDirection($direction)
    {
        $direction = strtolower($direction);
        if ($direction != self::DIR_ASC) {
            $direction = self::DIR_DESC;
        }

        return $direction;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $queryArray     = $this->applyPagination($this->applyExplain($this->applyFilters($this->generateQueryArray())));
        $this->rawQuery = json_encode($queryArray);

        return $queryArray;
    }

    /**
     * @param array $query
     *
     * @return array
     */
    protected function applyFilters($query)
    {
        foreach ($this->filters as $field => $value) {
            $query['post_filter']['bool']['must'][] = ['term' => [$field => $value]];
        }

        return $this->filterExcludedMerchants($query);
    }

    /**
     * Apply excluded merchant filter.
     *
     * @param array $query
     *
     * @return mixed
     */
    protected function filterExcludedMerchants(array $query)
    {
        if (!empty($this->excludedMerchants)) {
            $query['post_filter']['bool']['must_not'][] = ['terms' => ['id' => $this->excludedMerchants]];
        }

        return $query;
    }

    /**
     * @param array $query
     *
     * @return array
     */
    protected function applyExplain(array $query)
    {
        if ($this->explain) {
            $query['explain'] = true;
        }

        return $query;
    }

    /**
     * @param array $query
     *
     * @return array
     */
    protected function applyPagination($query)
    {
        $query['from'] = (int)(($this->page - 1) * $this->pageSize);
        $query['size'] = (int)$this->pageSize;

        return $query;
    }

    /**
     * There are weird cases here where you escape && as \&&, which is why we don't use addcslashes.
     *
     * @param string $searchTerm
     */
    protected function setSearchTerm($searchTerm)
    {
        self::setReservedArrayList(self::ES_RESERVED_CHARS);

        $termList         = array_filter(explode(' ', $searchTerm));
        $cleaned          = array_map([self::class, 'processSearchTerm'], $termList);
        $this->searchTerm = implode(' ', $cleaned);
    }

    /**
     * Remove reserved word if at the start or beginning of a word.
     *
     * @param string $term
     *
     * @return string
     */
    protected static function processSearchTerm($term)
    {
        foreach (self::getReservedArrayList() as $reserved) {
            $term = self::escapeWord($reserved, $term);
        }

        return $term;
    }

    /**
     * @param string $reserved
     * @param string $term
     *
     * @return mixed
     */
    private static function escapeWord($reserved, $term)
    {
        $length        = strlen($term);
        $reserveLength = strlen($reserved);
        if ($length > strlen($reserved) &&
            (
                strpos($term, $reserved) === 0 ||
                strpos($term, $reserved) === ($length - $reserveLength)
            )
        ) {
            $term = str_replace($reserved, "\\$reserved", $term);
        }

        return $term;
    }

    /**
     * @return array
     */
    private static function getReservedArrayList()
    {
        return self::$reservedArrayList;
    }

    /**
     * @param string $reservedArrayList
     */
    private function setReservedArrayList($reservedArrayList)
    {
        self::$reservedArrayList = array_filter(explode(' ', $reservedArrayList));
    }

    /**
     * @return WordFilter
     */
    protected static function getNameFilter()
    {
        return self::$nameFilter;
    }

    /**
     * @param WordFilter $nameFilter
     */
    public static function setNameFilter($nameFilter = null)
    {
        self::$nameFilter = $nameFilter;
    }

    /**
     * Convert to lower case for case insensitive exact match
     * The not_analyzed categories.exact_match field is populated with lowercase values.
     *
     * @return string
     */
    public function getSearchTerm()
    {
        if ($filter = $this->getNameFilter()) {
            return $filter->filter($this->searchTerm, ['language' => $this->getLanguage()]);
        }

        return strtolower($this->searchTerm);
    }

    /**
     * Return exact search term as entered.
     *
     * @return string
     */
    public function getOriginalSearchTerm()
    {
        return $this->searchTerm;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return array
     */
    public function prepareSortOrder()
    {
        if (empty($this->sortOrder)) {
            return $this->defaultSortOrder;
        }

        return $this->sortOrder;
    }

    /**
     * @return array
     */
    abstract protected function generateQueryArray();

    /**
     * @return string
     */
    abstract public function getType();
}
