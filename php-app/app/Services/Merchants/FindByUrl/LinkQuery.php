<?php

namespace MapleSyrupGroup\Search\Services\Merchants\FindByUrl;

use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;
use MapleSyrupGroup\Search\Services\Merchants\DomainQuery;

final class LinkQuery implements DomainQuery
{
    /**
     * @var string
     */
    private $link;

    /**
     * @var int
     */
    private $domainId;

    /**
     * @param string $link
     * @param int    $domainId
     */
    public function __construct($link, $domainId)
    {
        $this->setLink($link);
        $this->domainId = $domainId;
    }

    /**
     * Clean link and prepend scheme if not set.
     *
     * @param string $url
     *
     * @throws InvalidUrlException
     */
    private function setLink($url)
    {
        $link         = trim(strip_tags(urldecode($url)));
        $originalLink = $link;
        if ($link && substr($link, 0, 4) != 'http') {
            $link = 'http://' . $link;
        }

        $link = filter_var($link, FILTER_SANITIZE_URL);
        if (filter_var($link, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED | FILTER_FLAG_SCHEME_REQUIRED) === false) {
            throw new InvalidUrlException(
                sprintf('URL [%s] is invalid', $originalLink),
                ExceptionCodes::CODE_MERCHANT_NOT_FOUND
            );
        }

        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getLink()
    {

        return $this->link;
    }

    /**
     * @return string
     */
    public function getLinkDomain()
    {
        $linkHost = parse_url($this->link, PHP_URL_HOST);

        return preg_replace('/^.*?([^.]+\.(co|com|org|net|gov)\.[^.]{2,}|[^.]+\.[^.]{2,})$/i', '$1', $linkHost);
    }

    /**
     * @return int
     */
    public function getDomainId()
    {
        return $this->domainId;
    }
}
