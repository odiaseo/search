<?php

namespace MapleSyrupGroup\Search\Models\Merchants\Filters;

class MerchantNameFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \MapleSyrupGroup\Search\Models\Merchants\Filters\MissingFilterOptionException
     */
    public function testThatExceptionIsThrownWithMissingLanguageOption()
    {
        (new MerchantNameFilter([]))->filter('keyword', []);
    }
}
