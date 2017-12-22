<?php

namespace MapleSyrupGroup\Search\Fractal\Transformers;

use League\Fractal\Resource\Item;
use MapleSyrupGroup\Annotations\Swagger\Annotations as SWG;
use MapleSyrupGroup\QCommon\Fractal\Transformers\Transformer;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Merchant;

/**
 *
 * @SWG\Model(
 *  id="Merchant",
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
 */
class MerchantTransformer extends Transformer
{
    /**
     * @param mixed $data
     *
     * @return Item
     */
    public function createResource($data)
    {
        return $this->item($data, $this);
    }

    /**
     * @param Merchant $merchant
     *
     * @return array
     */
    public function transform(Merchant $merchant)
    {
        return [
            'id' => $merchant->getId(),
            'name' => $merchant->getName(),
            'url_name' => $merchant->getUrlName(),
            'description' => $merchant->getDescription(),
        ];
    }
}
