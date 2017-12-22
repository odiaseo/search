<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries;

use MapleSyrupGroup\Search\Models\Merchants\Filters\MerchantNameFilter;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;
use Prophecy\Argument;

/**
 * Class QueryTest.
 */
class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider queryOptionProvider
     * @group query
     *
     * @param $queryClass
     * @param $language
     * @param $searchTerm
     * @param $domainId
     */
    public function testSearchQueryContainsTheSearchTermAndType($queryClass, $language, $searchTerm, $domainId)
    {
        $query = new Query($searchTerm, $domainId, new SortParameter());
        $query->setLanguage($language);

        $filter = $this->prophesize(MerchantNameFilter::class);
        $filter->getReplacements(Argument::cetera())->willReturn([]);
        $filter->filter(Argument::cetera())->willReturn($searchTerm);

        $esQuery    = $queryClass::fromQuery($query, $filter->reveal());
        $queryArray = $esQuery->generateQueryArray();
        $json       = json_encode($queryArray);

        $this->assertContains($searchTerm, $json);
        $this->assertSame('merchants', $esQuery->getType());
    }

    /**
     * @dataProvider queryOptionProvider
     * @group query
     *
     * @param $queryClass
     * @param $language
     * @param $searchTerm
     * @param $domainId
     */
    public function testSearchQueryContainsQueryParameterKeys($queryClass, $language, $searchTerm, $domainId)
    {
        $query = new Query($searchTerm, $domainId, new SortParameter());
        $query->setLanguage($language);
        $query->setExcludedMerchants('123,456');
        $query->setCategoryIds('123,456');

        $esQuery    = $queryClass::fromQuery($query);
        $queryArray = $esQuery->toArray();

        $this->assertArrayHasKey('query', $queryArray);
        $this->assertArrayHasKey('sort', $queryArray);
        $this->assertArrayHasKey('post_filter', $queryArray);
        $this->assertArrayHasKey('bool', $queryArray['post_filter']);
        $this->assertArrayHasKey('must', $queryArray['post_filter']['bool']);
        $this->assertArrayHasKey('must_not', $queryArray['post_filter']['bool']);
        $this->assertArrayHasKey('term', $queryArray['post_filter']['bool']['must'][0]);
    }

    public function queryOptionProvider()
    {
        return [
            [BestMatchQuery::class, 'english', 'fashion1', 1],
            [MerchantExactMatchQuery::class, 'english', 'fashion2', 1],
            [FallbackQuery::class, 'english', 'fashion3', 1],
            [RatesTextExactMatchQuery::class, 'english', 'fashion4', 1],
            [CategoryExactMatchQuery::class, 'english', 'fashion5', 1],
            [CategoryPrefixMatchQuery::class, 'english', 'fashion6', 1],

            [BestMatchQuery::class, 'french', 'la poste1', 200],
            [MerchantExactMatchQuery::class, 'french', 'la poste2', 200],
            [FallbackQuery::class, 'french', 'la poste3', 200],
        ];
    }
}
