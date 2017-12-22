<?php

namespace MapleSyrupGroup\Search\Services\Importer;

use Exception;
use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\OutOfRetries;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\RetryOccurred;
use MapleSyrupGroup\Search\Services\Importer\Exceptions\IndexDefinitionException;
use MapleSyrupGroup\Search\Services\Importer\Exceptions\OutOfRetriesException;
use MapleSyrupGroup\Search\Services\IndexBuilder\IndexBuilder;
use MapleSyrupGroup\Search\Services\IndexStatusTracker\IndexStatusTracker;
use Psr\Log\LoggerInterface;

/**
 * Import from a different data sources to create a search index.
 */
class ElasticaImporter implements Import
{
    /**
     * How long to back off each retry.
     *
     * Retry 1 == BACK_OFF * 1 sec
     * retry 2 == BACK_OFF * 2 sec
     * retry 3 == BACK_OFF * 3 sec
     */
    const BACK_OFF = 1;

    /**
     * @var IndexStatusTracker
     */
    private $searchIndexStatus;

    /**
     * @var IndexBuilder[]
     */
    private $indexBuilders;

    /**
     * @var int
     */
    private $retryCount;

    /**
     * @var BusinessEventLogger
     */
    private $businessEventLogger;

    /**
     * Time in seconds.
     *
     * @var int
     */
    private $backOff;

    /**
     * @var int
     */
    private $maximumRetries;

    /**
     * ImportImplementation constructor.
     *
     * @param array               $indexBuilders       Keyed by index name
     * @param IndexStatusTracker  $searchIndexStatus
     * @param BusinessEventLogger $businessEventLogger
     * @param int                 $retryCount
     * @param int                 $backOff
     */
    public function __construct(
        array $indexBuilders,
        IndexStatusTracker $searchIndexStatus,
        BusinessEventLogger $businessEventLogger,
        $retryCount = 0,
        $backOff = self::BACK_OFF
    ) {
        $this->setBackOff($backOff);
        $this->setSearchIndexStatus($searchIndexStatus);
        $this->setRetryCount($retryCount);
        $this->setBusinessEventLogger($businessEventLogger);
        $this->setIndexBuilders($indexBuilders);
        $this->setMaximumRetries($retryCount);
    }

    /**
     * Import documents from different clients to make them searchable.
     *
     * @param string          $index
     * @param null|int        $domainId
     * @param LoggerInterface $output
     * @param mixed           $statusId
     *
     * @throws IndexDefinitionException
     * @throws OutOfRetriesException
     *
     * @return bool
     */
    public function doImport($index, $domainId, LoggerInterface $output, $statusId = null)
    {
        if ($this->isIndexBuildingInProgress($domainId, $output, $statusId)) {
            return false;
        }

        $this->getSearchIndexStatus()->lock($domainId, [], $statusId);

        $matchingDefinitions = $this->getImporters($index, $domainId);

        if (empty($matchingDefinitions)) {
            throw new IndexDefinitionException(
                sprintf('Index [%s] for domain [%s] is not defined in system', $index, (string) $domainId)
            );
        }

        $this->process($index, $matchingDefinitions, $output);

        return $this->getSearchIndexStatus()->unlock($domainId, $statusId);
    }

    /**
     * @param null|integer $domainId
     * @param LoggerInterface $output
     * @param $statusId
     *
     * @return bool
     */
    private function isIndexBuildingInProgress($domainId, LoggerInterface $output, $statusId)
    {
        if ($this->getSearchIndexStatus()->isRunning($domainId, $statusId)) {
            $statusInfo = $this->searchIndexStatus->getStatus($domainId, $statusId);
            $output->warning((string) $statusInfo);

            return true;
        }

        return false;
    }

    /**
     * @param string          $index
     * @param array           $matchingDefinitions
     * @param LoggerInterface $output
     */
    private function process($index, $matchingDefinitions, LoggerInterface $output)
    {
        /* @var IndexBuilder $indexBuilder */
        foreach ($matchingDefinitions as $indexDefinitionKey) {
            try {
                $indexBuilder = $this->getIndexBuilders($indexDefinitionKey);
                $this->buildIndex($index, $indexDefinitionKey, $indexBuilder, $output, $this->getRetryCount());
            } catch (OutOfRetriesException $exception) {
                $this->businessEventLogger->log(
                    new OutOfRetries($index, $exception, 0, $this->retryCount)
                );
            }
        }
    }

    /**
     * Build single index and continue to retry if an exception occurs for specified $retryCount.
     *
     * @param string          $index
     * @param string          $indexName
     * @param IndexBuilder    $indexBuilder
     * @param LoggerInterface $output
     * @param int             $retryCount
     *
     * @return bool
     *
     * @throws OutOfRetriesException
     */
    private function buildIndex($index, $indexName, IndexBuilder $indexBuilder, LoggerInterface $output, $retryCount)
    {
        $success = false;

        do {
            try {
                $indexBuilder->build($indexName, $output);
                $success = true;
            } catch (\Exception $exception) {
                $retryCount = $this->handleBuildFailure($exception, $output, $index, $retryCount);
            }
        } while (!$success);

        return $success;
    }

    /**
     * Log build exceptions, throw exception if number of retries is exhausted.
     *
     * @param Exception       $exception
     * @param LoggerInterface $output
     * @param string          $index
     * @param int             $retryCount
     *
     * @return int
     *
     * @throws OutOfRetriesException
     */
    private function handleBuildFailure(Exception $exception, LoggerInterface $output, $index, $retryCount)
    {
        $context = [$exception];

        if ($retryCount <= 0) {
            throw new OutOfRetriesException('Out of retries!', ExceptionCodes::CODE_OUT_OF_RETRIES, $exception);
        }

        $waitTime   = $this->getBackOff() * $retryCount;
        $numRetries = $this->getMaximumRetries();
        $output->warning("Import failed, retrying $retryCount/{$numRetries} in $waitTime sec", $context);

        $this->getBusinessEventLogger()->log(
            new RetryOccurred($index, $exception, $retryCount, $numRetries, $waitTime)
        );

        sleep($waitTime);
        $this->setRetryCount(--$retryCount);

        return $retryCount;
    }

    /**
     * Get importers we can use for this combination of index and domain id.
     *
     * @param string $index
     * @param int    $domainId
     *
     * @return array
     */
    private function getImporters($index, $domainId)
    {
        $matchingDefinitions = array_filter(
            array_keys($this->getIndexBuilders()),
            function ($key) use ($index, $domainId) {

                if ($domainId === null) {
                    return substr($key, 0, strlen($index)) === $index;
                }

                return $key === ($index . '_' . $domainId);
            }
        );

        return $matchingDefinitions;
    }

    /**
     * @return IndexStatusTracker
     */
    public function getSearchIndexStatus()
    {
        return $this->searchIndexStatus;
    }

    /**
     * @param IndexStatusTracker $searchIndexStatus
     */
    private function setSearchIndexStatus(IndexStatusTracker $searchIndexStatus)
    {
        $this->searchIndexStatus = $searchIndexStatus;
    }

    /**
     * @param string $indexDefinitionKey
     *
     * @return IndexBuilder|IndexBuilder[]
     */
    public function getIndexBuilders($indexDefinitionKey = null)
    {
        if (!empty($indexDefinitionKey) && isset($this->indexBuilders[$indexDefinitionKey])) {
            return $this->indexBuilders[$indexDefinitionKey];
        }

        return $this->indexBuilders;
    }

    /**
     * @param array $indexBuilders
     */
    private function setIndexBuilders($indexBuilders)
    {
        $this->indexBuilders = $indexBuilders;
    }

    /**
     * @return int
     */
    public function getRetryCount()
    {
        return $this->retryCount;
    }

    /**
     * @param int $retryCount
     */
    private function setRetryCount($retryCount)
    {
        $this->retryCount = $retryCount;
    }

    /**
     * @return BusinessEventLogger
     */
    public function getBusinessEventLogger()
    {
        return $this->businessEventLogger;
    }

    /**
     * @param BusinessEventLogger $businessEventLogger
     */
    private function setBusinessEventLogger($businessEventLogger)
    {
        $this->businessEventLogger = $businessEventLogger;
    }

    /**
     * @return int
     */
    public function getBackOff()
    {
        return $this->backOff;
    }

    /**
     * @param int $backOff
     */
    public function setBackOff($backOff)
    {
        $this->backOff = (int) $backOff;
    }

    /**
     * @return int
     */
    public function getMaximumRetries()
    {
        return $this->maximumRetries;
    }

    /**
     * @param int $maximumRetries
     */
    public function setMaximumRetries($maximumRetries)
    {
        $this->maximumRetries = (int) $maximumRetries;
    }
}
