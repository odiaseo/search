<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Stubs\ConcreteElasticsearchQuery;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;

class ElasticsearchQueryTest extends \PHPUnit_Framework_TestCase
{
    const SEARCH_TERM = 'foo';

    const DOMAIN_ID = 200;

    public function testItIsCreatedFromAQuery()
    {
        $query = $this->createQuery();
        $query->setStatus('active');
        $query->setLanguage('french');
        $query->setPage(10);
        $query->setPageSize(15);

        $esQuery = ConcreteElasticsearchQuery::fromQuery($query);

        $this->assertInstanceOf(ConcreteElasticsearchQuery::class, $esQuery);
        $this->assertSame(self::SEARCH_TERM, $esQuery->getSearchTerm());
        $this->assertArraySubset([ConcreteElasticsearchQuery::DOMAIN_ID_FIELD => self::DOMAIN_ID],
            $esQuery->getFilters());
        $this->assertArraySubset(['status' => 'active'], $esQuery->getFilters());
        $this->assertSame('french', $esQuery->getLanguage());
        $this->assertSame(10, $esQuery->getPage());
        $this->assertSame(15, $esQuery->getPageSize());
        $this->assertArrayNotHasKey('explain', $esQuery->toArray(), 'Explain is not enabled by default');
    }

    public function testDebugQueryEnablesExplain()
    {
        $query = $this->createQuery();
        $query->setDebug(true);

        $esQuery = ConcreteElasticsearchQuery::fromQuery($query);

        $this->assertInstanceOf(ConcreteElasticsearchQuery::class, $esQuery);
        $this->assertArraySubset(['explain' => true], $esQuery->toArray());
    }

    /**
     * @dataProvider provideSearchTermsWithSpecialCharacters
     */
    public function testItEscapesSpecialCharactersInTheSearchTerm($searchTerm, $expectedSearchTerm)
    {
        $esQuery = ConcreteElasticsearchQuery::fromQuery($this->createQuery($searchTerm));

        $this->assertSame($expectedSearchTerm, $esQuery->getSearchTerm());
    }

    public function testThatTheSearchTermIsLowercasedWhenNotFilterIsDefined()
    {
        $term  = 'UPPERCASE';
        $query = ConcreteElasticsearchQuery::fromQuery($this->createQuery($term));
        $this->assertSame(strtolower($term), $query->getSearchTerm());
    }

    public function provideSearchTermsWithSpecialCharacters()
    {
        return [
            //reserved char after term
            ['+test', '\\+test'],
            ['-test', '\\-test'],
            ['=test', '\\=test'],
            ['&&test', '\\&&test'],
            ['||test', '\\||test'],
            ['>test', '\\>test'],
            ['<test', '\\<test'],
            ['!test', '\\!test'],

            //bracket exempted
            ['(test', '(test'],
            [')test', ')test'],

            //reserved char before term
            ['test{', 'test\\{'],
            ['test}', 'test\\}'],
            ['test[', 'test\\['],
            ['test]', 'test\\]'],
            ['test^', 'test\\^'],
            ['test"', 'test\\"'],
            ['test~', 'test\\~'],
            ['test*', 'test\\*'],
            ['test?', 'test\\?'],
            ['test:', 'test\\:'],
            ['test\\', 'test\\\\'],
            ['test/', 'test\\/'],

            //multiple reserved chars
            [
                '+test =test test&& foo ^test bar test|| baz \\test',
                '\\+test \\=test test\&& foo \\^test bar test\\|| baz \\\\test',
            ],

            //reserved char with no space in merchant name
            ['123-reg.co.uk', '123-reg.co.uk'],
            ['My-picture.co.uk', 'my-picture.co.uk'],
            ['uk power - compare energy', 'uk power - compare energy'],

            //reserved char with space in merchant name
            ['123 -reg.co.uk', '123 \\-reg.co.uk'],
        ];
    }

    /**
     * @param string $searchTerm
     * @param int $domainId
     *
     * @return Query
     */
    public function createQuery($searchTerm = self::SEARCH_TERM, $domainId = self::DOMAIN_ID)
    {
        return new Query($searchTerm, $domainId, new SortParameter());
    }
}
