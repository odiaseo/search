<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Search;

/**
 * @group time-sensitive
 */
class TimerTest extends \PHPUnit_Framework_TestCase
{
    public function testItMeasuresTimeTakenToSearch()
    {
        $timer = new Timer();

        $timer->searchStarted('foo');

        sleep(2);

        $timer->searchFinished('foo');

        $this->assertEquals(2.0, $timer->getTimeTaken('foo'), '', 0.1);
    }

    public function testItReturnsTimeTakenSoFarIfSearchHasNotFinishedYet()
    {
        $timer = new Timer();

        $timer->searchStarted('foo');

        sleep(3);

        $this->assertEquals(3.0, $timer->getTimeTaken('foo'), '', 0.1);
    }

    /**
     * @expectedException \LogicException
     */
    public function testItThrowsAnExceptionIfSearchIsFinishedButItWasNeverStarted()
    {
        $timer = new Timer();

        $timer->searchFinished('foo');
    }

    public function testItExposesSearchAttempts()
    {
        $timer = new Timer();

        $timer->searchStarted('foo');
        sleep(4);
        $timer->searchFinished('foo');

        $timer->searchStarted('bar');
        sleep(2);
        $timer->searchFinished('bar');

        $this->assertEquals(
            [
                ['strategy' => 'foo', 'took' => 4.0],
                ['strategy' => 'bar', 'took' => 2.0],
            ],
            $timer->getSearchAttempts(),
            '',
            0.1
        );
    }
}
