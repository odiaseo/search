<?php

namespace MapleSyrupGroup\Search\Services\Merchants\FindByUrl;

class MerchantTest extends \PHPUnit_Framework_TestCase
{
    const ID = 13;

    const NAME = 'Amazon';

    const URL_NAME = 'amazon';

    const DESCRIPTION = 'Foo bar baz.';

    const LINKS = ['http://www.amazon.com/', 'http://www.amazon.co.uk'];

    public function testItExposesItsProperties()
    {
        $merchant = new Merchant(self::ID, self::NAME, self::URL_NAME, self::DESCRIPTION, self::LINKS);

        $this->assertSame(self::ID, $merchant->getId());
        $this->assertSame(self::NAME, $merchant->getName());
        $this->assertSame(self::URL_NAME, $merchant->getUrlName());
        $this->assertSame(self::DESCRIPTION, $merchant->getDescription());
        $this->assertSame(self::LINKS, $merchant->getLinks());
    }

    public function testItReturnsTheNameAsAStringRepresentation()
    {
        $merchant = new Merchant(self::ID, self::NAME, self::URL_NAME, self::DESCRIPTION, self::LINKS);

        $this->assertSame(self::NAME, (string) $merchant);
    }
}
