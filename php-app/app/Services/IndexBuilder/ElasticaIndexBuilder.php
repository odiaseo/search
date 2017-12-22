<?php

namespace MapleSyrupGroup\Search\Services\IndexBuilder;

use Elastica\Exception\ClientException;
use Exception;
use InvalidArgumentException;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEventLogger as Logger;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\Failure;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\Started;
use MapleSyrupGroup\Search\Services\BusinessEventLogger\BusinessEvents\Import\Success;
use MapleSyrupGroup\Search\Services\Client\SearchClient;
use MapleSyrupGroup\Search\Services\Importer\Documents\Hydrators\DocumentHydrator;
use MapleSyrupGroup\Search\Services\Importer\Exceptions\IndexBuildException;
use MapleSyrupGroup\Search\Services\Importer\Types\StaticTypeBuilder;
use MapleSyrupGroup\Search\Services\IndexBuilder\Index\Index;
use Psr\Log\LoggerInterface;

/**
 * Build an index and all the types associated with it.
 */
class ElasticaIndexBuilder implements IndexBuilder
{
    /**
     * @var StaticTypeBuilder[]
     */
    private $typeBuilders;

    /**
     * @var SearchClient
     */
    private $client;

    /**
     * @var DocumentHydrator[]
     */
    private $documentProvider;

    /**
     * @var array
     */
    private $config;

    /**
     * @var Logger
     */
    private $businessEventLogger;

    /**
     * @var string
     */
    private $tempIndexName;

    /**
     * @var Index
     */
    private $currentIndex;

    /**
     * @var integer
     */
    private $tempSuffix;

    /**
     * IndexBuilder constructor.
     *
     * @param SearchClient $client
     * @param array        $config
     * @param array        $builders
     * @param array        $providers
     * @param Logger       $logger
     * @param string       $suffix
     */
    public function __construct(SearchClient $client, array $config, array $builders, array $providers, Logger $logger, $suffix = '')
    {
        $this->setClient($client);
        $this->setTypeBuilders($builders);
        $this->setDocumentProvider($providers);
        $this->setConfig($config);
        $this->setBusinessEventLogger($logger);
        $this->setTempSuffix($suffix);

    }

    /**
     * Build an index.
     *
     * @param string          $indexName
     * @param LoggerInterface $output
     *
     * @throws IndexBuildException
     */
    public function build($indexName, LoggerInterface $output)
    {
        $startTime    = microtime(true);
        $currentIndex = null;

        try {
            $this->setTempIndexName($indexName . '_' . $this->getTempSuffix());
            $this->prepareBuild($indexName, $output);
            $output->debug(sprintf('Using temporary name "%s" ... ', $this->getTempIndexName()));
            $currentIndex = $this->getCurrentIndex();

            $currentIndexes = $this->buildIndexTypes($currentIndex, $output, $indexName);
            $oldIndexes     = $this->removePreviousIndexes($currentIndexes, $output);

            $this->logSuccessEvent($indexName, $oldIndexes, $startTime);
        } catch (Exception $exception) {
            $this->handleBuildException($indexName, $exception, $startTime, $currentIndex);
        }

        $output->info('Done!');
    }

    /**
     * @param Index           $index
     * @param LoggerInterface $output
     *
     * @return bool
     */
    private function checkNewIndexExist(Index $index, LoggerInterface $output)
    {
        $tempIndex = $this->getTempIndexName();
        $output->debug("Checking new index '{$tempIndex}' exists ... ");

        $response = $index->request('_cat/indices', 'GET')->getData();
        $exists   = isset($response['_index']) && preg_match('#' . preg_quote($tempIndex) . '#', $response['_index']);

        if ($exists) {
            return true;
        }

        throw new InvalidArgumentException($tempIndex . ' ... index does not exist: ' . print_r($response, true));
    }

    /**
     * @param string $indexName
     * @param array  $oldIndexes
     * @param int    $startTime
     */
    private function logSuccessEvent($indexName, $oldIndexes, $startTime)
    {
        $endTime = microtime(true) - $startTime;
        $count   = (int) $this->getCurrentIndex()->count();
        $success = new Success($this->getTempIndexName(), $indexName, $oldIndexes, $endTime, $count);
        $this->getBusinessEventLogger()->log($success);
    }

    /**
     * @param string          $indexName
     * @param LoggerInterface $output
     */
    private function prepareBuild($indexName, LoggerInterface $output)
    {
        $output->info("(Re)Building index '$indexName'...");
        $indexName = $this->getTempIndexName();
        $this->getBusinessEventLogger()->log(new Started($indexName, $indexName));

        $index = $this->getClient()->getIndex($indexName);
        $index->create($this->getConfig());

        $this->setCurrentIndex($index);
    }

    /**
     * @param Index           $index
     * @param LoggerInterface $output
     * @param string          $indexName
     *
     * @return Index[]
     */
    private function buildIndexTypes(Index $index, LoggerInterface $output, $indexName)
    {
        foreach ($this->getTypeBuilders() as $typeBuilder) {
            $typeBuilder->build($index, $output);
        }

        $indexes = $index->getClient()->getStatus()->getIndicesWithAlias($indexName);

        $this->populateIndexes($index, $output);
        $this->checkNewIndexExist($index, $output);
        $index->addAlias($indexName, true);
        $output->debug(sprintf("Aliasing new index '%s' to $indexName ...", $this->getTempIndexName()));

        return $indexes;
    }

    /**
     * @param Index           $index
     * @param LoggerInterface $output
     */
    private function populateIndexes(Index $index, LoggerInterface $output)
    {
        /** @var DocumentHydrator $docProvider */
        foreach ($this->getDocumentProvider() as $docProvider) {
            $docProvider->hydrate($index, $output);
        }
    }

    /**
     * @param Index[]         $currentIndexes
     * @param LoggerInterface $output
     *
     * @return array
     */
    private function removePreviousIndexes(array $currentIndexes, LoggerInterface $output)
    {
        $oldIndexes = [];
        foreach ($currentIndexes as $currentIndex) {
            $output->debug("Removing old index '{$currentIndex->getName()}' ... ");
            $oldIndexes[] = $currentIndex->getName();
            $currentIndex->delete();
        }

        return $oldIndexes;
    }

    /**
     * @param string    $indexName
     * @param Exception $exception
     * @param int       $startTime
     * @param Index     $index
     *
     * @throws IndexBuildException
     */
    private function handleBuildException($indexName, Exception $exception, $startTime, Index $index = null)
    {
        $failure = new Failure($this->getTempIndexName(), $indexName, $exception, microtime(true) - $startTime);
        try {
            if ($index instanceof Index) {
                $index->delete();
            }
        } catch (ClientException $e) {
            // connection might have gone away but we still want to log the error (follows)
        }

        $this->getBusinessEventLogger()->log($failure);

        throw new IndexBuildException('Error Building Index', 0, $exception);
    }

    /**
     * @return string
     */
    private function getTempIndexName()
    {
        return $this->tempIndexName;
    }

    /**
     * @param string $tempIndexName
     */
    private function setTempIndexName($tempIndexName)
    {
        $this->tempIndexName = $tempIndexName;
    }

    /**
     * @return Index
     */
    private function getCurrentIndex()
    {
        return $this->currentIndex;
    }

    /**
     * @param Index $currentIndex
     */
    private function setCurrentIndex(Index $currentIndex)
    {
        $this->currentIndex = $currentIndex;
    }

    /**
     * @return SearchClient
     */
    private function getClient()
    {
        return $this->client;
    }

    /**
     * @param SearchClient $client
     */
    private function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return StaticTypeBuilder[]
     */
    private function getTypeBuilders()
    {
        return $this->typeBuilders;
    }

    /**
     * @param StaticTypeBuilder[] $typeBuilders
     */
    private function setTypeBuilders($typeBuilders)
    {
        $this->typeBuilders = $typeBuilders;
    }

    /**
     * @return DocumentHydrator[]
     */
    private function getDocumentProvider()
    {
        return $this->documentProvider;
    }

    /**
     * @param DocumentHydrator[] $documentProvider
     */
    private function setDocumentProvider($documentProvider)
    {
        $this->documentProvider = $documentProvider;
    }

    /**
     * @return array
     */
    private function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    private function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return Logger
     */
    private function getBusinessEventLogger()
    {
        return $this->businessEventLogger;
    }

    /**
     * @param Logger $businessEventLogger
     */
    private function setBusinessEventLogger($businessEventLogger)
    {
        $this->businessEventLogger = $businessEventLogger;
    }

    /**
     * @return int
     */
    public function getTempSuffix()
    {
        return $this->tempSuffix;
    }

    /**
     * @param mixed $tempSuffix
     *
     * @return ElasticaIndexBuilder
     */
    public function setTempSuffix($tempSuffix)
    {
        $tempSuffix       = $tempSuffix ?: time();
        $this->tempSuffix = $tempSuffix;

        return $this;
    }
}
