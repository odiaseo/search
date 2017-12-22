<?php

namespace MapleSyrupGroup\Search\Fractal\Transformers;

use Illuminate\Contracts\Foundation\Application;
use League\Fractal\Resource\Item;
use MapleSyrupGroup\Annotations\Swagger\Registry;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Stubs\MerchantFactory;

class MerchantTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group fractal
     */
    public function testMerchantHitTransformCanCreateResource()
    {
        $app = $this->prophesize(Application::class);
        $reg = $this->prophesize(Registry::class);

        $transformer = new MerchantTransformer($app->reveal());
        $transformer->setSwaggerRegistry($reg->reveal());

        $resource = $transformer->createResource([]);

        $this->assertInstanceOf(Item::class, $resource);
    }

    /**
     * @group fractal
     */
    public function testTransformerTranformsMerchantData()
    {
        $merchant    = MerchantFactory::withDefaults();
        $app         = $this->prophesize(Application::class);
        $transformer = new MerchantTransformer($app->reveal());

        $result = $transformer->transform($merchant);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('url_name', $result);
        $this->assertArrayHasKey('description', $result);
    }
}
