<?php
/**
 * SearchResultsReturnedEvent.php
 * Definition of class SearchResultsReturnedEvent.
 *
 * Created 30-Sep-2016, 12:20:41 PM
 *
 * @author M.D.Ward <md.ward@quidco.com>
 * Copyright (c) 2016, Maple Syrup Media Ltd
 */

namespace MapleSyrupGroup\Search\Events;

/**
 * SearchResultsReturnedEvent.
 *
 * @author M.D.Ward <md.ward@quidco.com>
 */
class SearchResultsReturnedEvent
{
    /**
     * @var int
     */
    private $topMerchantId;

    /**
     * @var string
     */
    private $topMerchantName;

    /**
     * @var string
     */
    private $givenSearchTerm;

    /**
     * @param int    $topMerchantId
     * @param string $topMerchantName
     * @param string $givenSearchTerm
     */
    public function __construct(
        $topMerchantId,
        $topMerchantName,
        $givenSearchTerm
    ) {
        $this
            ->setTopMerchantId($topMerchantId)
            ->setTopMerchantName($topMerchantName)
            ->setGivenSearchTerm($givenSearchTerm)
        ;
    }

    /**
     * @return int
     */
    public function getTopMerchantId()
    {
        return $this->topMerchantId;
    }

    /**
     * @return string
     */
    public function getTopMerchantName()
    {
        return $this->topMerchantName;
    }

    /**
     * @return string
     */
    public function getGivenSearchTerm()
    {
        return $this->givenSearchTerm;
    }

    /**
     * @param int $topMerchantId
     *
     * @return $this
     */
    public function setTopMerchantId($topMerchantId)
    {
        $this->topMerchantId = (int) $topMerchantId;

        return $this;
    }

    /**
     * @param string $topMerchantName
     *
     * @return $this
     */
    public function setTopMerchantName($topMerchantName)
    {
        $this->topMerchantName = (string) $topMerchantName;

        return $this;
    }

    /**
     * @param string $givenSearchTerm
     *
     * @return $this
     */
    public function setGivenSearchTerm($givenSearchTerm)
    {
        $this->givenSearchTerm = (string) $givenSearchTerm;

        return $this;
    }
}
