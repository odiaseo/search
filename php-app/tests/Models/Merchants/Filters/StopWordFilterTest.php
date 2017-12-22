<?php

namespace MapleSyrupGroup\Search\Models\Merchants\Filters;

class StopWordFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider searchTermDataProvider
     */
    public function testThatStopWordsAreFiltered($searchTerm, $language, $expected)
    {
        $stopWords = [
            $language => [
                'rates_text' => [
                    'and',
                    'sales',
                    'or',
                ],
            ],
        ];

        $options = ['language' => $language, 'field' => 'rates_text'];
        $result  = (new StopWordFilter($stopWords))->filter($searchTerm, $options);
        $this->assertSame($expected, $result);
    }

    public function testThatSearchTermIsReturnedTheSameWhenNoSopWordExist()
    {
        $term    = ['word'];
        $options = ['language' => 'english', 'field' => 'rates_text'];
        $result  = (new StopWordFilter([]))->filter($term, $options);
        $this->assertSame($term, $result);
    }


    /**
     * @expectedException \MapleSyrupGroup\Search\Models\Merchants\Filters\MissingFilterOptionException
     */
    public function testThatExceptionIsThrownWithMissingLanguageOption()
    {
        $options = ['field' => 'rates_text'];
        (new StopWordFilter([]))->filter('keyword', $options);
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Models\Merchants\Filters\MissingFilterOptionException
     */
    public function testThatExceptionIsThrownWithMissingFieldOption()
    {
        $options = ['language' => 'french'];
        (new StopWordFilter([]))->filter('keyword', $options);
    }

    public function searchTermDataProvider()
    {
        return [
            [['marks and spencer'], 'english', ['marks spencer']],
            [['£20 sales today'], 'french', ['today']],
            [['5 star hotel or free tickets'], 'english', ['5 star hotel free tickets']],
        ];
    }
}
