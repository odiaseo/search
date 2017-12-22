<?php
/**
 * NewRelicAttributeAddingListenerTest.php
 * Definition of class NewRelicAttributeAddingListenerTest
 *
 * Created 30-Sep-2016, 12:43:11 PM
 *
 * @author M.D.Ward <md.ward@quidco.com>
 * Copyright (c) 2016, Maple Syrup Media Ltd
 */

namespace MapleSyrupGroup\Search\Listeners;

use Exception;
use MapleSyrupGroup\QCommon\External\NewRelic\NewRelicAttributeAdder;
use MapleSyrupGroup\Search\Events\SearchResultsReturnedEvent;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface as LoggerInterface;

/**
 * NewRelicAttributeAddingListenerTest
 *
 * @author M.D.Ward <md.ward@quidco.com>
 */
class NewRelicAttributeAddingListenerTest extends TestCase
{

    /**
     *
     * @var NewRelicAttributeAdder
     */
    private $adder;

    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     *
     * @var NewRelicAttributeAddingListener
     */
    private $listener;


    protected function setUp()
    {
        $this->adder  = $this->prophesize(NewRelicAttributeAdder::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->listener = new NewRelicAttributeAddingListener(
            $this->adder->reveal(),
            $this->logger->reveal()
        );
    }

    public function testOnSearchResultsReturnedEventPassedParametersToAttributeAdder()
    {
        $event = $this->prophesize(SearchResultsReturnedEvent::class);

        $event
            ->getTopMerchantId()
            ->willReturn(($topMerchantId = 12345))
            ->shouldBeCalled();

        $this->adder
            ->addAttribute(
                Argument::is('merchant_id'),
                Argument::is($topMerchantId)
            )
            ->shouldBeCalled();

        $event
            ->getTopMerchantName()
            ->willReturn(($topMerchantName = 'TOP MERCHANT NAME'))
            ->shouldBeCalled();

        $this->adder
            ->addAttribute(
                Argument::is('merchant_name'),
                Argument::is($topMerchantName)
            )
            ->shouldBeCalled();

        $event
            ->getGivenSearchTerm()
            ->willReturn(($givenSearchTerm = 'given+search+terms'))
            ->shouldBeCalled();

        $this->adder->addAttribute(
            Argument::is('given_search_term'),
            Argument::is($givenSearchTerm)
        )->shouldBeCalled();

        $this->listener->onSearchResultsReturned($event->reveal());
    }

    public function testOnSearchResultsReturnsLogsAnyException()
    {
        $event = $this->prophesize(SearchResultsReturnedEvent::class);

        $event
            ->getTopMerchantId()
            ->willReturn(($topMerchantId = 12345))
            ->shouldBeCalledTimes(1);

        $this->adder
            ->addAttribute(
                Argument::is('merchant_id'),
                Argument::is($topMerchantId)
            )
            ->willThrow(($ex = new Exception("EXCEPTION MESSAGE")))
            ->shouldBeCalledTimes(1);

        $this->logger
            ->warning(
                Argument::is(
                    sprintf(
                        "Unable to submit attributes to New Relic; %s - %s",
                        get_class($ex),
                        $ex->getMessage()
                    )
                )
            )
            ->shouldBeCalledTimes(1);

        $this->listener->onSearchResultsReturned($event->reveal());
    }

}
