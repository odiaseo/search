<?php

namespace MapleSyrupGroup\Search\Http\Controllers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Pagination\LengthAwarePaginator;
use MapleSyrupGroup\QCommon\Exceptions\AuthorisationException;
use MapleSyrupGroup\QCommon\Exceptions\Exception;
use MapleSyrupGroup\Search\Events\SearchResultsReturnedEvent;
use MapleSyrupGroup\Search\Http\Controllers\Parameter\MerchantSearchParameterResolver;
use MapleSyrupGroup\Search\Http\Request\ApiRequest;
use MapleSyrupGroup\Search\Services\Merchants\Search;
use MapleSyrupGroup\Annotations\Swagger\Annotations as SWG;

/**
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   resourcePath="/search",
 *   description="Operations about search",
 *   produces="['application/json']"
 * )
 */
class SearchController extends AbstractController
{
    /**
     * @var Search
     */
    private $search;

    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    /**
     * @param Search $search
     * @param Dispatcher $eventDispatcher
     */
    public function __construct(Search $search, Dispatcher $eventDispatcher)
    {
        $this->setSearch($search)
            ->setEventDispatcher($eventDispatcher);

        parent::__construct();
    }

    /**
     * @SWG\Api(
     *   path="/search/merchant",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="Search term in merchants",
     *     notes="Searches the given term among all merchants using the search v4 algorithm.",
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
     *       required=true,
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="language",
     *       description="The language we are searching for merchants with",
     *       required=false,
     *       defaultValue="english",
     *       enum="['english','french']",
     *       type="string",
     *       paramType="query",
     *       allowMultiple=false
     *     ),
     *     @SWG\Parameter(
     *       name="status",
     *       description="Filter merchant by status",
     *       required=false,
     *       enum="['active','paused','scheduled','draft','pending','expired']",
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
     *       name="exclude_merchants",
     *       description="Ids of merchants to exclude from search results",
     *       type="array",
     *       items={
     *          "type"="integer"
     *       },
     *       required=false,
     *       paramType="query",
     *     ),
     *     @SWG\Parameter(
     *       name="in_store",
     *       description="Flag to filter merchants based on is_in_store attribute. Leave empty to ignore filter",
     *       type="string",
     *       enum="['1', '0']",
     *       required=false,
     *       paramType="query",
     *     ),
     *     @SWG\Parameter(
     *       name="debug",
     *       description="Enables the debug mode",
     *       type="boolean",
     *       required=false,
     *       defaultValue="false",
     *       paramType="query"
     *     ),
     *     @SWG\Parameter(
     *       name="strategy",
     *       description="Attempt using the specified strategy(s) when performing the search. Defaults to all",
     *       required=false,
     *       enum="['exact_match','prefix_match','category_exact_match','category_prefix_match','most_relevant','last_resort']",
     *     type="string", paramType="query", allowMultiple=true
     *     ),
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
    public function searchMerchant(ApiRequest $request)
    {
        return $this->processRequest(function () use ($request) {
            $query = (new MerchantSearchParameterResolver())->createQuery($request, $this->domainId);

            $result = $this->getSearch()->search($query);

            $this->dispatchResult($query->getSearchTerm(), $result);

            return $this->paginateResponse($result, $query);
        });
    }

    /**
     * @return Search
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param Search $search
     *
     * @return SearchController
     */
    public function setSearch(Search $search)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @return Dispatcher
     */
    protected function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @param Dispatcher $eventDispatcher
     *
     * @return $this
     */
    protected function setEventDispatcher(Dispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * @param string $searchTerm
     * @param array $result
     */
    protected function dispatchResult($searchTerm, array $result)
    {
        // @todo move this logic into a factory
        if (!isset($result['hits']['hits'][0]['_source']['id'])
            || !isset($result['hits']['hits'][0]['_source']['name'])
        ) {
            return;
        }

        $details = $result['hits']['hits'][0]['_source'];

        $this->getEventDispatcher()->fire(
            new SearchResultsReturnedEvent(
                (int)$details['id'],
                $details['name'],
                $searchTerm
            )
        );
    }
}
