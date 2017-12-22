<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Search;

use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Search\SearchCompletedEvent;
use MapleSyrupGroup\Search\Services\Merchants\Query;
use MapleSyrupGroup\Search\Services\Merchants\Search;
use MapleSyrupGroup\Search\Services\Merchants\SortParameter;
use MapleSyrupGroup\Search\Services\Merchants\Stubs\SearchFailedException;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @group search
 */
class SearchStrategyCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FallbackSearch
     */
    private $search;

    /**
     * @var Search|ObjectProphecy
     */
    private $firstSearchStrategy;

    /**
     * @var Search|ObjectProphecy
     */
    private $secondSearchStrategy;

    /**
     * @var Search|ObjectProphecy
     */
    private $thirdSearchStrategy;

    /**
     * @var Timer|ObjectProphecy
     */
    private $timer;

    /**
     * @var BusinessEventLogger|ObjectProphecy
     */
    private $eventLogger;

    protected function setUp()
    {
        $this->firstSearchStrategy = $this->prophesize(Search::class);
        $this->secondSearchStrategy = $this->prophesize(Search::class);
        $this->thirdSearchStrategy = $this->prophesize(Search::class);
        $this->eventLogger = $this->prophesize(BusinessEventLogger::class);
        $this->timer = $this->prophesize(Timer::class);

        $this->search = new SearchStrategyCollection(
            [
                'first' => $this->firstSearchStrategy->reveal(),
                'second' => $this->secondSearchStrategy->reveal(),
                'third' => $this->thirdSearchStrategy->reveal(),
            ],
            $this->eventLogger->reveal(),
            $this->timer->reveal(),
            [ '1' => ['first', 'third'], '200' => ['first']],
            true
        );
    }

    public function testItIsSearch()
    {
        $this->assertInstanceOf(Search::class, $this->search);
    }

    public function testItReturnsRestultSetOfTheFirstSuccessfulSearchStrategy()
    {
        $query = $this->getQuery();
        $resultSet = ['foo' => 'bar'];

        $this->firstSearchStrategy->search($query)->willReturn($resultSet);

        $returnedResultSet = $this->search->search($query);

        $this->assertArraySubset($resultSet, $returnedResultSet);
    }

    public function testItFallsBackToTheNextStrategyIfOneFails()
    {
        $query = $this->getQuery();
        $resultSet = ['foo' => 'bar'];

        $this->firstSearchStrategy->search($query)->willThrow(new SearchFailedException('Search 1 failed.'));
        $this->secondSearchStrategy->search($query)->willThrow(new SearchFailedException('Search 2 failed.'));
        $this->thirdSearchStrategy->search($query)->willReturn($resultSet);

        $returnedResultSet = $this->search->search($query);

        $this->assertArraySubset($resultSet, $returnedResultSet);
    }

    /**
     * @expectedException \LogicException
     */
    public function testItThrowsTheLogicExceptionIfNoStrategiesWereSuccessful()
    {
        $this->firstSearchStrategy->search(Argument::any())->willThrow(new SearchFailedException('Search 1 failed.'));
        $this->secondSearchStrategy->search(Argument::any())->willThrow(new SearchFailedException('Search 2 failed.'));
        $this->thirdSearchStrategy->search(Argument::any())->willThrow(new SearchFailedException('Search 3 failed.'));

        $this->search->search($this->getQuery());
    }

    public function testItRegistersTriedStrategiesOnTheResponse()
    {
        $query = $this->getQuery();
        $query->setStrategy(['first']);
        
        $attemptedStrategies = [['strategy' => 'first', 'took' => 2]];
        $resultSet = ['foo' => 'bar', 'strategy' => 'first', 'attempted_strategies' => $attemptedStrategies];
        $this->timer->searchStarted('first')->willReturn();
        $this->timer->searchFinished('first')->willReturn();
        $this->timer->getTimeTaken('first')->willReturn(2);
        $this->timer->getSearchAttempts()->willReturn($attemptedStrategies);

        $this->firstSearchStrategy->search($query)->willReturn($resultSet);

        $returnedResultSet = $this->search->search($query);

        $this->assertArrayHasKey('strategy', $returnedResultSet, print_r($returnedResultSet, true));
        $this->assertSame('first', $returnedResultSet['strategy']);
        $this->assertArrayHasKey('attempted_strategies', $returnedResultSet);
        $this->assertSame($attemptedStrategies, $returnedResultSet['attempted_strategies']);
    }

    public function testItLogsASuccessfulSearch()
    {
        $query = $this->getQuery();

        $this->firstSearchStrategy->search($query)->willReturn(['foo' => 'bar']);

        $this->search->search($query);

        $this->eventLogger->log(Argument::type(SearchCompletedEvent::class))->shouldHaveBeenCalled();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider domainStrategyProvider
     *
     * @param $domainStrategy
     */
    public function testExceptionIsThrownWithInvalidDomainStrategies($domainStrategy)
    {
        $firstSearchStrategy = $this->prophesize(Search::class);
        $this->eventLogger   = $this->prophesize(BusinessEventLogger::class);
        $this->timer         = $this->prophesize(Timer::class);

        $search = new SearchStrategyCollection(
            [
                'first' => $firstSearchStrategy->reveal(),

            ],
            $this->eventLogger->reveal(),
            $this->timer->reveal(),
            $domainStrategy,
            false
        );

        $search->search($this->getQuery());
    }

    public function domainStrategyProvider()
    {
        return [
            [[]],
            [[1, 2, 3]],
            [[1 => ['does_not_exist']]],
        ];
    }
    private function getQuery()
    {
        return new Query('foo', 1, new SortParameter());
    }
}
