<?php
/**
 * NewRelicAttributeAddingListener.php
 * Definition of class NewRelicAttributeAddingListener.
 *
 * Created 30-Sep-2016, 1:24:06 PM
 * 
 * @author M.D.Ward <md.ward@quidco.com>
 * Copyright (c) 2016, Maple Syrup Media Ltd
 */

namespace MapleSyrupGroup\Search\Listeners;

use Exception;
use MapleSyrupGroup\QCommon\External\NewRelic\NewRelicAttributeAdder;
use MapleSyrupGroup\Search\Events\SearchResultsReturnedEvent;
use Psr\Log\LoggerInterface;

/**
 * NewRelicAttributeAddingListener.
 *
 * @author M.D.Ward <md.ward@quidco.com>
 */
class NewRelicAttributeAddingListener
{
    /**
     * @var NewRelicAttributeAdder
     */
    private $adder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor - creates a new instance of NewRelicAttributeAddingListener.
     * 
     * @param NewRelicAttributeAdder $adder
     * @param LoggerInterface        $logger
     */
    public function __construct(
        NewRelicAttributeAdder $adder,
        LoggerInterface $logger
    ) {
        $this
            ->setAdder($adder)
            ->setLogger($logger)
        ;
    }

    /**
     * @return NewRelicAttributeAdder
     */
    protected function getAdder()
    {
        return $this->adder;
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param NewRelicAttributeAdder $adder
     *
     * @return $this
     */
    protected function setAdder(NewRelicAttributeAdder $adder)
    {
        $this->adder = $adder;

        return $this;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param SearchResultsReturnedEvent $event
     */
    public function onSearchResultsReturned(SearchResultsReturnedEvent $event)
    {
        try {
            $this->doAddAttributes($event);
        } catch (Exception $ex) {
            $this->getLogger()->warning(
                sprintf(
                    'Unable to submit attributes to New Relic; %s - %s',
                    get_class($ex),
                    $ex->getMessage()
                )
            );
        }
    }

    /**
     * @param SearchResultsReturnedEvent $event
     *
     * @throws Exception
     */
    protected function doAddAttributes(SearchResultsReturnedEvent $event)
    {
        $adder = $this->getAdder();

        $adder->addAttribute(
            'merchant_id',
            $event->getTopMerchantId()
        );

        $adder->addAttribute(
            'merchant_name',
            $event->getTopMerchantName()
        );

        $adder->addAttribute(
            'given_search_term',
            $event->getGivenSearchTerm()
        );
    }
}
