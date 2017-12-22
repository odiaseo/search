<?php

namespace MapleSyrupGroup\Search\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use InvalidArgumentException;
use MapleSyrupGroup\Annotations\Swagger\Annotations as SWG;
use MapleSyrupGroup\QCommon\Exceptions\AuthorisationException;
use MapleSyrupGroup\QCommon\Exceptions\Exception;
use MapleSyrupGroup\QCommon\Exceptions\InvalidRequestException;
use MapleSyrupGroup\Search\Http\Request\ApiRequest;
use MapleSyrupGroup\Search\Http\Controllers\Parameter\CategoryMerchantParameterResolver;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\Search\CategoryExactMatchSearch;

/**
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   resourcePath="/search",
 *   description="Operations about searching for merchants by category",
 *   produces="['application/json']"
 * )
 */
class CategoryMerchantSearchController extends AbstractController
{
    /**
     * @var CategoryExactMatchSearch
     */
    protected $search;

    /**
     * @param CategoryExactMatchSearch $search
     */
    public function __construct(CategoryExactMatchSearch $search)
    {
        $this->setSearch($search);

        parent::__construct();
    }

    /**
     * @SWG\Api(
     *   path="/search/merchant/category",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="Search for merchants by category name",
     *     notes="Searches the given term among category names and returns merchants in the matched category",
     *     type="array",
     *     @SWG\Items("MerchantSearchHit"),
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
     *       name="search_term",
     *       description="Search term",
     *       type="string",
     *       allowMultiple=false,
     *       required=false,
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="language",
     *       description="The language we are searching for category with",
     *       required=false,
     *       defaultValue="english",
     *       enum="['english','french']",
     *       type="string",
     *       paramType="query",
     *       allowMultiple=false
     *     ),
     *     @SWG\Parameter(
     *       name="page_size",
     *       description="Results per page",
     *       type="integer",
     *       format="int32",
     *       minimum="1",
     *       maximum="1000.0",
     *       allowMultiple=false,
     *       required=false,
     *       defaultValue=40,
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="page",
     *       description="Page number to display",
     *       type="integer",
     *       format="int32",
     *       minimum="1",
     *       allowMultiple=false,
     *       required=false,
     *       defaultValue=1,
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="category_id",
     *       description="Ids of categories to limit the result if multiple categories match the search term",
     *       type="array",
     *       items={
     *          "type"="integer"
     *       },
     *       required=false,
     *       paramType="query",
     *     ),
     *     @SWG\Parameter(
     *       name="sort_field",
     *       description="Field to sort results by",
     *       type="string",
     *       enum="['relevance', 'popularity', 'cashback_amount', 'cashback_percentage']",
     *       allowMultiple=false,
     *       required=false,
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="sort_order",
     *       description="Sort order",
     *       type="string",
     *       allowMultiple=false,
     *       enum="['asc', 'desc']",
     *       required=false,
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="debug",
     *       description="Enables the debug mode",
     *       type="boolean",
     *       required=false,
     *       defaultValue="false",
     *       paramType="query"
     *     )
     *   )
     * )
     *
     * @param ApiRequest $request
     *
     * @return LengthAwarePaginator
     *
     * @throws Exception
     * @throws AuthorisationException
     */
    public function findMerchantsByCategory(ApiRequest $request)
    {
        return $this->processRequest(function () use ($request) {
            $query  = $this->getQueryFromRequest($request);
            $result = $this->getSearch()->search($query);

            return $this->paginateResponse($result, $query);
        });
    }

    /**
     * @param ApiRequest $request
     *
     * @return Query
     *
     * @throws Exception
     */
    private function getQueryFromRequest(ApiRequest $request)
    {
        try {
            return (new CategoryMerchantParameterResolver())->createQuery($request, $this->domainId);
        } catch (InvalidArgumentException $exception) {
            throw $this->exception(InvalidRequestException::class, $exception->getMessage(), $exception);
        }
    }

    /**
     * @return CategoryExactMatchSearch
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param CategoryExactMatchSearch $search
     */
    public function setSearch($search)
    {
        $this->search = $search;
    }
}
