<?php

namespace MapleSyrupGroup\Search\Fractal\Transformers;

use MapleSyrupGroup\Annotations\Swagger\Annotations as SWG;
use MapleSyrupGroup\QCommon\Fractal\Transformers\AutomatedTransformer as BaseTransformer;
use MapleSyrupGroup\Search\Services\IndexStatusTracker\StatusData;

/**
 *
 * @SWG\Model(
 *  id="SearchIndexStatus",
 *  required="status",
 *  @SWG\Property(
 *      name="id",
 *      type="string",
 *      description="unique identifier for the search index"
 *  ),
 *  @SWG\Property(
 *      name="status",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      name="created_at",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      name="updated_at",
 *      type="string"
 *  ),
 * )
 *
 */
class SearchIndexStatusTransformer extends BaseTransformer
{
    /**
     * Returns model class
     *
     * @return string
     */
    public function getModelClass()
    {
        return StatusData::class;
    }
}
