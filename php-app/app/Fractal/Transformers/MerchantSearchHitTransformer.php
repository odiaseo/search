<?php

namespace MapleSyrupGroup\Search\Fractal\Transformers;

use League\Fractal\Resource\Item;
use MapleSyrupGroup\Annotations\Swagger\Annotations as SWG;
use MapleSyrupGroup\QCommon\Fractal\Transformers\Transformer;

/**
 *
 * @SWG\Model(
 *  id="MerchantSearchHit",
 *  @SWG\Property(
 *      name="id",
 *      type="integer",
 *      format="int64",
 *      minimum="0.0",
 *      description="unique identifier for merchant"
 *  ),
 *  @SWG\Property(
 *      name="name",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      name="url_name",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      name="description",
 *      type="string"
 *  )
 * )
 *
 */
class MerchantSearchHitTransformer extends Transformer
{
    /**
     * @param $data
     *
     * @return Item
     */
    public function createResource($data)
    {
        return $this->item($data, $this);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function transform(array $data)
    {
        $extras = [
            'score' => $data['_score'],
        ];

        if (isset($data['_explanation'])) {
            $extras['debug'] = ['explanation' => $data['_explanation']];
        }

        return array_merge($data['_source'], $extras);
    }
}
