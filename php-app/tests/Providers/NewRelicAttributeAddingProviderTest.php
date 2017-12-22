<?php
/**
 * NewRelicAttributeAddingProviderTest.php
 * Definition of class NewRelicAttributeAddingProviderTest
 *
 * Created 02-Oct-2016, 6:42:18 PM
 *
 * @author M.D.Ward <md.ward@quidco.com>
 * Copyright (c) 2016, Maple Syrup Media Ltd
 */

namespace MapleSyrupGroup\Search\Providers;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use MapleSyrupGroup\QCommon\External\NewRelic\NewRelicAttributeAdder;
use MapleSyrupGroup\Search\Events\SearchResultsReturnedEvent;
use MapleSyrupGroup\Search\Listeners\NewRelicAttributeAddingListener;
use PHPUnit_Framework_Assert as Assert;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

/**
 * NewRelicAttributeAddingProviderTest
 *
 * @author M.D.Ward <md.ward@quidco.com>
 */
class NewRelicAttributeAddingProviderTest extends TestCase
{

    /**
     *
     * @var Application
     */
    private $application;

    /**
     *
     * @var NewRelicAttributeAddingProvider
     */
    private $provider;


    protected function setUp()
    {
        $this->application = $this->prophesize(Application::class);

        $this->provider = new NewRelicAttributeAddingProvider($this->application->reveal());
    }

    public function testRegisterCreatesListenerSingleton()
    {
        $app = $this->application;

        $app
            ->bound(Argument::is(NewRelicAttributeAdder::class))
            ->willReturn(false)
            ->shouldBeCalledTimes(1);

        $logger = $this->prophesize(LoggerInterface::class);

        $logger
            ->info(
                Argument::is(
                    'A request to send New Relic Attributes was received, '
                    . 'but will not be processed as this is not a production instance '
                    . 'or the New Relic extension is not loaded.'
                )
            )
            ->shouldBeCalledTimes(1);

        $app
            ->make(Argument::is('log'))
            ->willReturn($logger->reveal())
            ->shouldBeCalledTimes(2);

        $app
            ->singleton(
                Argument::is(NewRelicAttributeAdder::class),
                Argument::type(Closure::class)
            )
            ->will(
                function (array $args) use ($app) {
                    $closure = $args[1];
                    $adder   = $closure($app->reveal());

                    Assert::assertInstanceOf(
                        NewRelicAttributeAdder::class,
                        $adder
                    );

                    $adder->addAttribute("", "");
                }
            )
            ->shouldBeCalledTimes(1);

        $app
            ->make(Argument::is(NewRelicAttributeAdder::class))
            ->willReturn(
                $this->prophesize(NewRelicAttributeAdder::class)->reveal()
            )
            ->shouldBeCalledTimes(1);

        $app
            ->singleton(
                Argument::is(NewRelicAttributeAddingListener::class),
                Argument::type(Closure::class)
            )
            ->will(
                function (array $args) use ($app) {
                    $closure  = $args[1];
                    $listener = $closure($app->reveal());

                    Assert::assertInstanceOf(
                        NewRelicAttributeAddingListener::class,
                        $listener
                    );
                }
            )
            ->shouldBeCalledTimes(1);

        $this->provider->register();
    }

    public function testBootSubscribesEvent()
    {
        $event = $this->prophesize(SearchResultsReturnedEvent::class);

        $listener = $this->prophesize(NewRelicAttributeAddingListener::class);
        $listener
            ->onSearchResultsReturned(Argument::is($event->reveal()))
            ->shouldBeCalledTimes(1);

        $this->application
            ->make(Argument::is(NewRelicAttributeAddingListener::class))
            ->willReturn($listener->reveal())
            ->shouldBeCalledTimes(1);

        $dispatcher = $this->prophesize(Dispatcher::class);
        $dispatcher
            ->listen(
                Argument::is(SearchResultsReturnedEvent::class),
                Argument::type(Closure::class)
            )
            ->will(
                function (array $args) use ($event) {
                    $closure = $args[1];

                    $closure($event->reveal());
                }
            )
            ->shouldBeCalledTimes(1);

        $this->application
            ->make(Argument::is('events'))
            ->willReturn($dispatcher->reveal())
            ->shouldBeCalledTimes(1);

        $this->provider->boot();
    }

}
