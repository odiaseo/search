<?php
/**
 * SearchResultsReturnedEventTest.php
 * Definition of class SearchResultsReturnedEventTest
 *
 * Created 30-Sep-2016, 11:48:33 AM
 *
 * @author M.D.Ward <md.ward@quidco.com>
 * Copyright (c) 2016, Maple Syrup Media Ltd
 */

namespace MapleSyrupGroup\Search\Events;

use PHPUnit_Framework_TestCase as TestCase;


/**
 * SearchResultsReturnedEventTest
 *
 * @author M.D.Ward <md.ward@quidco.com>
 */
class SearchResultsReturnedEventTest extends TestCase
{

    /**
     *
     * @var integer
     */
    private $merchantId = 5;

    /**
     *
     * @var string
     */
    private $merchantName = "Marks and Spencers";

    /**
     *
     * @var string
     */
    private $givenSearchTerm = "given+search+term";

    /**
     *
     * @var SearchResultsReturnedEvent
     */
    private $event;


    protected function setUp()
    {
        $this->event = new SearchResultsReturnedEvent(
            $this->merchantId,
            $this->merchantName,
            $this->givenSearchTerm
        );
    }

    public function testGetTopMerchantIdReturnsInjectedValue()
    {
        $this->assertSame(
            $this->merchantId,
            $this->event->getTopMerchantId()
        );
    }

    public function testGetTopMerchantNameReturnsInjectedValue()
    {
        $this->assertSame(
            $this->merchantName,
            $this->event->getTopMerchantName()
        );
    }

    public function testGetGivenSearchTermReturnsInjectedSearchTerm()
    {
        $this->assertSame(
            $this->givenSearchTerm,
            $this->event->getGivenSearchTerm()
        );
    }

}
