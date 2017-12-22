<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\ElasticsearchQuery;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\LinkQuery;

class WildcardLinkQueryTest extends \PHPUnit_Framework_TestCase
{
    const LINK = 'http://www.amazon.co.uk/';

    const DOMAIN_ID = 1;

    public function testItIsCreatedWithANamedConstructor()
    {
        $linkQuery = new LinkQuery(self::LINK, self::DOMAIN_ID);
        $query = WildcardLinkQuery::fromLinkQuery($linkQuery);

        $this->assertInstanceOf(WildcardLinkQuery::class, $query);
    }

    public function testItIsAnElasticsearchQuery()
    {
        $linkQuery = new LinkQuery(self::LINK, self::DOMAIN_ID);
        $query = WildcardLinkQuery::fromLinkQuery($linkQuery);

        $this->assertInstanceOf(ElasticsearchQuery::class, $query);
    }

    public function testItsTypeIsMerchants()
    {
        $linkQuery = new LinkQuery(self::LINK, self::DOMAIN_ID);
        $query = WildcardLinkQuery::fromLinkQuery($linkQuery);

        $this->assertSame('merchants', $query->getType());
    }

    public function testItGeneratesAnElasticsearchQueryArray()
    {
        $linkQuery = new LinkQuery(self::LINK, self::DOMAIN_ID);
        $query = WildcardLinkQuery::fromLinkQuery($linkQuery);

        $queryArray = $query->toArray();

        $this->assertInternalType('array', $queryArray);
        $this->assertArrayHasKey('query', $queryArray);
        $this->assertQueryContains(['toolbar_opt_out' => false], $queryArray);
        $this->assertQueryContains(['links' => '*' . $linkQuery->getLinkDomain() . '*'], $queryArray);
        $this->assertQueryContains([ElasticsearchQuery::DOMAIN_ID_FIELD => self::DOMAIN_ID], $queryArray);
    }

    private function assertQueryContains(array $search, array $queryArray)
    {
        $searchJson = json_encode($search);
        $queryJson = json_encode($queryArray);

        $this->assertTrue(
            false !== strpos($queryJson, $searchJson),
            sprintf('Expected \'%s\' to be part of \'%s\'.', $searchJson, $queryJson)
        );
    }
}
