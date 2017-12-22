<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\Queries\Factory;

use MapleSyrupGroup\Search\Services\Merchants\Elasticsearch\QueryFactory;
use MapleSyrupGroup\Search\Services\Merchants\Query;

abstract class QueryFactoryTestCase extends \PHPUnit_Framework_TestCase
{
    const TERM = 'foo';

    const DOMAIN_ID = 1;

    const ENGLISH = Query::LANGUAGE_ENGLISH;

    const FRENCH = Query::LANGUAGE_FRENCH;

    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    protected function setUp()
    {
        $this->queryFactory = $this->createQueryFactory();
    }

    abstract protected function createQueryFactory();

    public function testItIsAQueryFactory()
    {
        $this->assertInstanceOf(QueryFactory::class, $this->queryFactory);
    }
}
