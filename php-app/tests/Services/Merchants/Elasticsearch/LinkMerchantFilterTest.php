<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Elasticsearch;

use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Stubs\MerchantFactory;

class LinkMerchantFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideMerchantHits
     */
    public function testItReturnsTheMerchantWithMatchingLink($link, $expectedMerchant, $merchants, $message = '')
    {
        $filter = new LinkMerchantFilter($merchants);

        $this->assertSame($expectedMerchant, $filter->__invoke($link), $message);
    }

    public function provideMerchantHits()
    {
        return [
            [
                'http://www.amazon.com',
                $merchant = MerchantFactory::withLinks(['amazon.com']),
                [$merchant],
                'Link matches a shorter domain',
            ],
            [
                'https://www.amazon.com/videos',
                $merchant = MerchantFactory::withLinks(['amazon.com']),
                [$merchant],
                'HTTPS Link matches a shorter domain',
            ],
            [
                'http://www.boutique-centella.com',
                $merchant = MerchantFactory::withLinks(['boutique-centella.com']),
                [$merchant],
                'Link matches a shorter domain',
            ],
            [
                'http://www.amazon.com/foo/bar',
                $merchant = MerchantFactory::withLinks(['amazon.com']),
                [$merchant],
                'Link with path matches a shorter domain',
            ],
            [
                'http://www.amazon.com',
                $merchant = MerchantFactory::withLinks(['amazon.com', 'www.amazon.com']),
                [MerchantFactory::withLinks(['amazon.fr']), $merchant],
                'Any of the links needs to match',
            ],
            [
                'http://www.amazon.com',
                $merchant = MerchantFactory::withLinks(['www.amazon.com']),
                [MerchantFactory::withLinks(['amazon.co.uk']), $merchant],
                'Link matches the exact sub-domain',
            ],
            [
                'http://www.nike.com/fr/foo/bar',
                $merchant = MerchantFactory::withLinks(['nike.com/fr']),
                [MerchantFactory::withLinks(['nike.com/en']), $merchant],
                'Link matches the domain with path',
            ],
            [
                'http://www.nike.com/fr/foo/bar',
                $merchant = MerchantFactory::withLinks(['nike.com/fr']),
                [$merchant, MerchantFactory::withLinks(['nike.com'])],
                'The first matched merchant is returned',
            ],
        ];
    }

    /**
     * @expectedException \MapleSyrupGroup\Search\Services\Merchants\FindByUrl\NoMerchantFoundException
     * @dataProvider provideMerchantMisses
     */
    public function testItThrowsNoMerchantFoundExceptionIfNoMerchantLinkMatches($link, $merchants)
    {
        (new LinkMerchantFilter($merchants))->__invoke($link);
    }

    public function provideMerchantMisses()
    {
        return [
            [
                'http://www.amazon.com',
                [MerchantFactory::withLinks(['video.amazon.com', 'amazon.co.uk'])],
            ],
            [
                'http://amazon.com',
                [MerchantFactory::withLinks(['www.amazon.com'])],
            ],
            [
                'http://www.amazon.com',
                [MerchantFactory::withLinks(['video.amazon.com', 'amazon.co.uk'])],
            ],
            [
                'http://www.nike.com/fr/foo/bar',
                [MerchantFactory::withLinks(['nike.com/en']), MerchantFactory::withLinks(['store.nike.com/fr'])],
            ],
            [
                'http://www.nike.com/foo/bar/amazon.com',
                [MerchantFactory::withLinks(['amazon.com'])],
            ],
        ];
    }
}
