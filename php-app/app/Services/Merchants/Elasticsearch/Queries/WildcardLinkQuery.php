<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchQuery;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\LinkQuery;

final class WildcardLinkQuery extends ElasticsearchQuery
{
    const TYPE = 'merchants';

    /**
     * @param LinkQuery $linkQuery
     *
     * @return WildcardLinkQuery
     */
    public static function fromLinkQuery(LinkQuery $linkQuery)
    {
        $query                                 = new self($linkQuery->getLinkDomain());
        $query->filters[self::DOMAIN_ID_FIELD] = $linkQuery->getDomainId();

        return $query;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->applyFilters($this->generateQueryArray());
    }

    /**
     * @return array
     */
    protected function generateQueryArray()
    {
        return [
            'query' => [
                'bool' => [
                    'filter' => ['term' => ['toolbar_opt_out' => false]],
                    'must'   => ['wildcard' => ['links' => sprintf('*%s*', $this->searchTerm)]],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }
}
