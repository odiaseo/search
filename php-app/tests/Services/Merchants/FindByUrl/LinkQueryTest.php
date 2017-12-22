<?php

namespace MapleSyrupGroup\Search\Services\Merchants\FindByUrl;

use MapleSyrupGroup\Search\Services\Merchants\DomainQuery;

class LinkQueryTest extends \PHPUnit_Framework_TestCase
{
    const LINK = 'http://amazon.com/';

    const DOMAIN_ID = 1;

    public function testItIsADOmainQuery()
    {
        $this->assertInstanceOf(DomainQuery::class, new LinkQuery(self::LINK, self::DOMAIN_ID));
    }

    public function testItExposesTheUrlAndDomain()
    {
        $query = new LinkQuery(self::LINK, self::DOMAIN_ID);

        $this->assertSame(self::LINK, $query->getLink());
        $this->assertSame(self::DOMAIN_ID, $query->getDomainId());
    }

    /**
     * @dataProvider provideInvalidLinks
     * @expectedException \MapleSyrupGroup\Search\Services\Merchants\FindByUrl\InvalidUrlException
     */
    public function testExceptionIsThrownWithInvalidLink($link)
    {
        new LinkQuery($link, self::DOMAIN_ID);
    }

    /**
     * @dataProvider provideLinks
     * @group debug
     */
    public function testItExtractsTheBaseDomainOfLink($link, $expectedLinkDomain)
    {
        $query  = new LinkQuery($link, self::DOMAIN_ID);
        $domain = $query->getLinkDomain();

        $this->assertSame($expectedLinkDomain, $domain);
    }

    public function provideInvalidLinks()
    {
        return [
            ['http//amazon.com'],
            ['httpwww.amazon.com/foo/bar?baz=1'],
            ['http.//www.amazon.com'],
        ];
    }

    public function provideLinks()
    {
        return [
            ['http://amazon.com', 'amazon.com'],
            ['http://amazon.com/foo/bar?baz=1', 'amazon.com'],
            ['amazon.com', 'amazon.com'],
            ['video.amazon.com', 'amazon.com'],
            ['http://amazon.co.uk', 'amazon.co.uk'],
            ['http://video.amazon.co.uk', 'amazon.co.uk'],
            ['http://store.nike.com', 'nike.com'],
            ['http://www.boutique-centella.com/', 'boutique-centella.com'],
            ['http://www.ihg.com/holidayinn/hotels/gb/en/reservation', 'ihg.com'],
            ['https://www.myd-design.com/', 'myd-design.com'],
            ['shop.giorgiofedon1919-watch.com', 'giorgiofedon1919-watch.com'],
            ['www.aerosus.fr', 'aerosus.fr'],
            ['www.solaris-sunglass.com', 'solaris-sunglass.com'],
            ['www.toledano-mag.com', 'toledano-mag.com'],
            ['www.variance-auto.com', 'variance-auto.com'],
            ['www.variance-auto.com?<script>alert();</script>', 'variance-auto.com'],
            [urlencode('www.variance-auto.com?<script>alert();</script>'), 'variance-auto.com'],
            ['http://www.w3schoo��ls.co�m', 'w3schools.com'],
            [
                'https%3A%2F%2Fwww.google.fr%2Fmaps%2F%4043.0695287%2C5.8731534%2C17zhttps%3A%2F%2Fwww.google.fr%2Fmaps%2Fsearch%2Fst%2Bmartin%2Bdu%2Bmont%2F%4046.0784711%2C5.2883641%2C12.5z',
                'google.fr',
            ],
            [
                'https%3A%2F%2Fwww.google.fr%2Fmaps%2Fplace%2F478%2BV.C.N%2B245%2BChemin%2Bde%2BManoir%2BVivo%2B%25C3%25A0%2Bla%2BVerne%2C%2B83500%2BLa%2BSeyne-sur-Mer%2F%4043.0695287%2C5.8731534%2C17z%2Fdata%3D!4m5!3m4!1s0x12c9033c8ea1d3a3%3A0x9e55b2c2eb1e5ba8!8m2!3d43.0742762!4d5.881892',
                'google.fr',
            ],
            [
                'https://www.google.fr/maps/place/478+V.C.N+245+Chemin+de+Manoir+Vivo+%C3%A0+la+Verne,+83500+La+Seyne-sur-Mer/@43.0695287,5.8731534,17z/data=!4m5!3m4!1s0x12c9033c8ea1d3a3:0x9e55b2c2eb1e5ba8!8m2!3d43.0742762!4d5.881892',
                'google.fr',
            ],
        ];
    }
}
