<?php

namespace MapleSyrupGroup\Search\Services\Merchants\Search;

use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;

class Timer
{
    /**
     * @var float
     */
    private $startTime;

    /**
     * @var array
     */
    private $searchAttempts = [];

    /**
     * @param string $name
     */
    public function searchStarted($name)
    {
        $this->startTime = microtime(true);
        $this->searchAttempts[$name] = ['strategy' => $name, 'took' => null];
    }

    /**
     * @param string $name
     */
    public function searchFinished($name)
    {
        if (!isset($this->searchAttempts[$name])) {
            throw new \LogicException(
                sprintf('The "%s" search has not been started.', $name),
                ExceptionCodes::CODE_SEARCH_NOT_STARTED
            );
        }

        $this->searchAttempts[$name]['took'] = $this->calculateTimeTaken();
    }

    /**
     * @param string $name
     *
     * @return float
     */
    public function getTimeTaken($name)
    {
        return $this->searchAttempts[$name]['took'] ?: $this->calculateTimeTaken();
    }

    /**
     * @return array
     */
    public function getSearchAttempts()
    {
        return array_values($this->searchAttempts);
    }

    /**
     * @return float
     */
    public function calculateTimeTaken()
    {
        return microtime(true) - $this->startTime;
    }
}
