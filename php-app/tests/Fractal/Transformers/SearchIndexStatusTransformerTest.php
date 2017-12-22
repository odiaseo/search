<?php

namespace MapleSyrupGroup\Search\Fractal\Transformers;

use Illuminate\Contracts\Foundation\Application;
use MapleSyrupGroup\Search\Services\IndexStatusTracker\StatusData;

class SearchIndexStatusTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group fractal
     */
    public function testTheTransformerModelClassIsSet()
    {
        $app         = $this->prophesize(Application::class);
        $transformer = new SearchIndexStatusTransformer($app->reveal());
        $class       = $transformer->getModelClass();

        $this->assertSame(StatusData::class, $class);
    }
}
