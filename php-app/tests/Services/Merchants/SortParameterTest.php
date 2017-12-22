<?php

namespace MapleSyrupGroup\Search\Services\Merchants;

use PHPUnit\Framework\TestCase;

class SortParameterTest extends TestCase
{
    /**
     * @dataProvider invalidSortParameterProvider
     * @expectedException \MapleSyrupGroup\Search\Services\Merchants\InvalidSortParameterException
     */
    public function testThatExceptionIsThrownWhenInvalidSortParametersAreSet($field, $direction)
    {
        new SortParameter($field, $direction);
    }

    public function invalidSortParameterProvider()
    {
        return [
            ['', 'asc'],
            ['', 'desc'],
            ['relevance', ''],
            ['popularity', ''],
            ['invalid', 'asc'],
            ['invalid', 'desc'],
        ];
    }

    public function testThatSortParametersCanBeSet()
    {
        $param = new SortParameter('relevance', 'desc');
        $query = new Query('shoes', 1, $param);

        $this->assertNotEmpty($query->getSortOrder());
        $this->assertSame('relevance', $query->getSortField());
        $this->assertSame('desc', $query->getSortDirection());
    }

    public function testThatSortParametersCanEmpty()
    {
        $param = new SortParameter('', '');
        $query = new Query('shoes', 1, $param);

        $this->assertEmpty($query->getSortField());
        $this->assertEmpty($query->getSortDirection());
    }
}
