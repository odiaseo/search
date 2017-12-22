<?php

namespace MapleSyrupGroup\Search\Http\Controllers;

use MapleSyrupGroup\Annotations\Swagger\Annotations as SWG;
use MapleSyrupGroup\Search\Http\Request\ApiRequest;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\LinkQuery;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Merchant;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Merchants;

/**
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   resourcePath="/search",
 *   description="Operations about search",
 *   produces="['application/json']"
 * )
 */
class FindMerchantByLinkController extends AbstractController
{
    /**
     * @var Merchants
     */
    private $merchants;

    /**
     * @param Merchants $merchants
     */
    public function __construct(Merchants $merchants)
    {
        $this->merchants = $merchants;

        parent::__construct();
    }

    /**
     * @SWG\Api(
     *   path="/search/merchant/link",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="Find the merchant for a given URL",
     *     notes="Matches an URL to a single merchant.",
     *     type="Merchant",
     *     authorizations={},
     *     @SWG\Parameter(
     *        name="Access-Token",
     *        paramType="header",
     *        defaultValue=1,
     *        description="Domain ID",
     *        required=true,
     *       type="integer",
     *       format="int32"
     *     ),
     *     @SWG\Parameter(
     *       name="link",
     *       description="Merchant link",
     *       type="string",
     *       allowMultiple=false,
     *       required=true,
     *       paramType="query"
     *     )
     *   )
     * )
     *
     * @param ApiRequest $request
     *
     * @return Merchant
     */
    public function findMerchant(ApiRequest $request)
    {
        return $this->processRequest(function () use ($request) {
            return $this->merchants->getByLink(new LinkQuery($request->queryParam('link'), $this->domainId));
        });
    }
}
