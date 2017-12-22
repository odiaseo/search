<?php

namespace MapleSyrupGroup\Search\Behat\Search;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Merchant as SearchMerchant;

final class MerchantRepository
{
    const MERCHANT_TYPE = 'merchants';

    const LANG_ENGLISH = 'english';
    const LANG_FRENCH  = 'french';

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $indexPrefix;

    /**
     * @var array
     */
    private $rawResponse;

    /**
     * @param Client $httpClient
     * @param string $indexPrefix
     */
    public function __construct(Client $httpClient, $indexPrefix)
    {
        $this->httpClient  = $httpClient;
        $this->indexPrefix = $indexPrefix;
    }

    /**
     * @param int    $domainId
     * @param array  $names
     * @param array  $exclude
     * @param int    $page
     * @param int    $limit
     * @param bool   $strict
     * @param string $language
     * @param mixed  $inStore
     * @param string $sortField
     * @param string $sortOrder
     *
     * @return Merchant[]
     */
    public function findMerchants(
        $domainId,
        array $names,
        $exclude = [],
        $page = 1,
        $limit = 40,
        $strict = true,
        $language = self::LANG_ENGLISH,
        $inStore = '',
        $sortField = '',
        $sortOrder = ''
    ) {
        $result      = [];
        $rawResponse = [];

        foreach ($names as $merchantName) {
            $params = [
                'testing'           => true,
                'search_term'       => $merchantName,
                'language'          => $language,
                'page'              => $page,
                'page_size'         => $limit,
                'exclude_merchants' => implode(',', $exclude),
                'in_store'          => $inStore,
                'sort_field'        => $sortField,
                'sort_order'        => $sortOrder,
            ];

            $endPoint = sprintf('search/merchant?%s', http_build_query($params));

            $response = $this->httpClient->request(
                'GET',
                $endPoint,
                [
                    'headers' => [
                        'Access-Token' => $domainId,
                    ],
                ]
            );

            if ($response->getStatusCode() === 200) {
                $data          = json_decode((string) $response->getBody(), true);
                $rawResponse[] = $data;

                foreach ($data['merchant_search_hits'] as $merchant) {
                    if (!$strict or $merchant['strategy'] === 'exact_match') {
                        $result[] = $merchant;
                    }
                }
            }
        }

        $this->rawResponse = $rawResponse;

        return $this->buildMerchants($result);
    }

    /**
     * @param int    $domainId
     * @param string $searchTerm
     * @param array  $excludedMerchants
     *
     * @return Merchant[]
     */
    public function search(
        $domainId,
        $searchTerm,
        $excludedMerchants = []
    ) {
        $result = [];

        $params = [
            'testing'           => true,
            'search_term'       => $searchTerm,
            'language'          => 'english',
            'page'              => 1,
            'page_size'         => 40,
            'exclude_merchants' => implode(',', $excludedMerchants),
        ];

        $endPoint = sprintf('search/merchant?%s', http_build_query($params));

        $response = $this->httpClient->request(
            'GET',
            $endPoint,
            [
                'headers' => [
                    'Access-Token' => $domainId,
                ],
            ]
        );

        if ($response->getStatusCode() === 200) {
            $data   = json_decode((string) $response->getBody(), true);
            $result = $data['merchant_search_hits'];
        }

        $this->rawResponse = $response;

        return $this->buildMerchants($result);
    }

    /**
     * @param int $domainId
     * @param string $merchantName
     * @param int $page
     * @param int $limit
     * @param string $language
     * @param bool $strict
     *
     * @return Merchant
     */
    public function findMerchant(
        $domainId,
        $merchantName,
        $page = 1,
        $limit = 1,
        $language = self::LANG_ENGLISH,
        $strict = true
    ) {
        $merchants = $this->findMerchants($domainId, [$merchantName], [], $page, $limit, $strict, $language);

        if (count($merchants)) {
            return $merchants[0];
        }

        throw new \LogicException(sprintf('Merchant not found: "%s".', $merchantName));
    }

    /**
     * @param int   $domainId
     * @param array $query
     *
     * @return Merchant[]
     */
    public function executeMerchantSearchQuery(
        $domainId,
        array $query
    ) {
        $result            = $this->httpClient->request(
            sprintf('/%s_%d/%s/_search', $this->indexPrefix, $domainId, self::MERCHANT_TYPE),
            'GET',
            $query
        )->getData();
        $this->rawResponse = $result;

        return $this->buildMerchants($result);
    }

    public function getByLink(
        $domainId,
        $link
    ) {
        try {
            $params = [
                'testing'  => true,
                'link'     => urlencode($link),
                'language' => 'french',
            ];

            $endPoint = sprintf('search/merchant/link?%s', http_build_query($params));
            $response = $this->httpClient->request(
                'GET',
                $endPoint,
                [
                    'headers' => [
                        'Access-Token' => $domainId,
                    ],
                ]
            );

            //if ($response->getStatusCode() === 200) {
            $data              = json_decode((string) $response->getBody(), true);
            $this->rawResponse = $data;

            if (isset($data['merchant'])) {
                $merchantData = current($data['merchant']);

                return new SearchMerchant(
                    $merchantData['id'],
                    $merchantData['name'],
                    $merchantData['url_name'],
                    $merchantData['description']
                );
            }
        } catch (ClientException $exception) {
            //404 is returned if not link is found
            if ($exception->getCode() === 404) {
                return;
            }

            throw $exception;
        }

        return [];
    }

    /**
     * @param int    $domainId
     * @param string $categoryName
     * @param array  $categoryId
     * @param int    $limit
     *
     * @return Merchant[]
     */
    public function findMerchantsByCategory(
        $domainId,
        $categoryName,
        array $categoryId,
        $limit = 40
    ) {
        $result = [];
        $params = [
            'testing'     => true,
            'search_term' => $categoryName,
            'language'    => 'english',
            'page'        => 1,
            'page_size'   => $limit,
            'category_id' => implode(',', array_filter($categoryId)),
        ];

        $endPoint = sprintf('search/merchant/category?%s', http_build_query($params));

        $response = $this->httpClient->request(
            'GET',
            $endPoint,
            [
                'headers' => [
                    'Access-Token' => $domainId,
                ],
            ]
        );

        if ($response->getStatusCode() === 200) {
            $data = json_decode((string) $response->getBody(), true);

            foreach ($data['merchant_search_hits'] as $merchant) {
                $result[] = $merchant;
            }
        }

        return $this->buildMerchants($result);
    }

    /**
     * @param array $result
     *
     * @return Merchant[]
     */
    private function buildMerchants(
        array $result
    ) {
        return array_map(function ($hit) {
            return new Merchant($hit);
        }, $result);
    }

    /**
     * @return array
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }
}
