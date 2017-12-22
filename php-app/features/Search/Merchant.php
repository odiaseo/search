<?php

namespace MapleSyrupGroup\Search\Behat\Search;

final class Merchant
{
    /**
     * @var array
     */
    private $source;

    /**
     * @param array $source
     */
    public function __construct(array $source)
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return (string) $this->source['name'];
    }

    /**
     * @return array
     */
    public function getRatesText()
    {
        return (array) $this->source['rates_text'];
    }

    /**
     * @return array
     */
    public function getKeywords()
    {
        return (array) $this->source['keywords'];
    }

    /**
     * @return array
     */
    public function getIsInStore()
    {
        return (int) $this->source['is_in_store'];
    }

    /**
     * @param string $keyword
     *
     * @return bool
     */
    public function hasKeyword($keyword)
    {
        return in_array($keyword, $this->getKeywords());
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->source['links'];
    }

    /**
     * @return string
     */
    public function getStrategy()
    {
        return (string) $this->source['strategy'];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
