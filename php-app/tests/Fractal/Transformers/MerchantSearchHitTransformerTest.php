<?php

namespace MapleSyrupGroup\Search\Fractal\Transformers;

use Illuminate\Contracts\Foundation\Application;
use League\Fractal\Resource\Item;
use MapleSyrupGroup\Annotations\Swagger\Registry;

class MerchantSearchHitTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group fractal
     */
    public function testMerchantHitTransformCanCreateResource()
    {
        $app = $this->prophesize(Application::class);
        $reg = $this->prophesize(Registry::class);

        $transformer = new MerchantSearchHitTransformer($app->reveal());
        $transformer->setSwaggerRegistry($reg->reveal());

        $resource = $transformer->createResource([]);

        $this->assertInstanceOf(Item::class, $resource);
    }

    /**
     * @group fractal
     */
    public function testTransformerTranformsSearchHits()
    {
        $data = [
            '_source' => [
                'hits' => []
            ],
            '_score'  => 12
        ];
        $app = $this->prophesize(Application::class);
        $transformer = new MerchantSearchHitTransformer($app->reveal());
        $result = $transformer->transform($data);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('score', $result);
        $this->assertArrayHasKey('hits', $result);
        $this->assertArrayNotHasKey('_source', $result);
        $this->assertArrayNotHasKey('_score', $result);
        $this->assertArrayNotHasKey('debug', $result);
    }

    /**
     * @group fractal
     */
    public function testTransformerIncludesExplanationDetailsIfPresent()
    {
        $data = [
            '_source' => [
                'hits' => []
            ],
            '_score'  => 12,
            '_explanation' => ['foo' => 'bar'],
        ];
        $app = $this->prophesize(Application::class);
        $transformer = new MerchantSearchHitTransformer($app->reveal());
        $result = $transformer->transform($data);

        $this->assertInternalType('array', $result);
        $this->assertArraySubset(['debug' => ['explanation' => ['foo' => 'bar']]], $result);
    }
}
