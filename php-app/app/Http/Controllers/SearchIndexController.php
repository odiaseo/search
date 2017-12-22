<?php

namespace MapleSyrupGroup\Search\Http\Controllers;

use Illuminate\Http\Response;
use MapleSyrupGroup\Annotations\Swagger\Annotations as SWG;
use MapleSyrupGroup\QCommon\Exceptions\Exception;
use MapleSyrupGroup\Search\Http\Request\ApiRequest;
use MapleSyrupGroup\Search\Console\Kernel;
use MapleSyrupGroup\Search\Services\IndexStatusTracker\IndexStatusTracker;

/**
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   resourcePath="/search",
 *   description="Operations about search",
 *   produces="['application/json']"
 * )
 */
class SearchIndexController extends AbstractController
{
    /**
     * @var IndexStatusTracker
     */
    private $statusTracker;

    /**
     * @param IndexStatusTracker $statusTracker
     */
    public function __construct(IndexStatusTracker $statusTracker)
    {
        $this->setStatusTracker($statusTracker);

        parent::__construct();
    }

    /**
     * @SWG\Api(
     *   path="/search/merchant/build/index",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="Build the Elastic Search index and return the status",
     *     notes="Call the artisan command to build the Elastic Search index and put it in the queue",
     *     type="SearchIndexStatus",
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
     *   )
     * )
     *
     * @param Kernel $kernel
     *
     * @return Response
     *
     * @throws Exception
     */
    public function updateIndexMerchant(Kernel $kernel)
    {
        return $this->processRequest(function () use ($kernel) {
            $statusId = $this->getStatusTracker()->getUniqueIdentifier();

            $this->getStatusTracker()->lock($this->domainId, [], $statusId);
            $kernel->queue('search:build-index', ['status_id' => $statusId]);

            return $this->respond($this->getStatusTracker()->getStatus($this->domainId, $statusId));
        });
    }

    /**
     * @SWG\Api(
     *   path="/search/merchant/build/index/status/{id}",
     *   @SWG\Operation(
     *     method="GET",
     *     summary="Get the status of the index",
     *     notes="Get the status of the index",
     *     type="SearchIndexStatus",
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
     *       name="id",
     *       description="Id of the build",
     *       type="string",
     *       allowMultiple=false,
     *       required=true,
     *       paramType="path"
     *     ),
     *  )
     * )
     *
     * @param ApiRequest $request
     *
     * @return Response
     *
     * @throws Exception
     */
    public function getMerchantIndexStatus(ApiRequest $request)
    {
        return $this->processRequest(function () use ($request) {
            $statusId = $request->pathParam('id');

            return $this->respond($this->getStatusTracker()->getStatus($this->domainId, $statusId));
        });
    }

    /**
     * @return IndexStatusTracker
     */
    public function getStatusTracker()
    {
        return $this->statusTracker;
    }

    /**
     * @param IndexStatusTracker $statusTracker
     *
     * @return SearchIndexController
     */
    public function setStatusTracker(IndexStatusTracker $statusTracker)
    {
        $this->statusTracker = $statusTracker;

        return $this;
    }
}
