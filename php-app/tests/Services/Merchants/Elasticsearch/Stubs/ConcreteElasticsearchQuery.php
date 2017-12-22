<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Stubs;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchQuery;

class ConcreteElasticsearchQuery extends ElasticsearchQuery
{
    /**
     * @return array
     */
    protected function generateQueryArray()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'stub';
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
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }
}
