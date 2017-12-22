<?php

namespace MapleSyrupGroup\Search\Exceptions\Factory;

use MapleSyrupGroup\Search\Exceptions\Http\BaseHttpException;
use MapleSyrupGroup\Search\Exceptions\SearchException;
use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\InvalidUrlException;

class HttpExceptionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testItCreatesHttpException()
    {
        $factory   = new HttpExceptionFactory(new InvalidUrlException('url is invalid'));
        $exception = $factory->create();
        $this->assertInstanceOf(BaseHttpException::class, $exception);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIExceptionIsThrownWithAnInvalidArgument()
    {
        $exception = $this->prophesize(SearchException::class);
        new HttpExceptionFactory($exception->reveal());
    }
}
