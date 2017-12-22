<?php

namespace MapleSyrupGroup\Search\Services\IndexBuilder\Index;

use Elastica\ResultSet\BuilderInterface;
use MapleSyrupGroup\Search\Services\Client\SearchClient;

interface Index
{
    /**
     * Returns a type object for the current index with the given name.
     *
     * @param string $type Type name
     *
     * @return mixed
     */
    public function getType($type);

    /**
     * Return Index Stats.
     *
     * @return mixed
     */
    public function getStats();

    /**
     * Gets all the type mappings for an index.
     *
     * @return array
     */
    public function getMapping();

    /**
     * Returns the index settings object.
     *
     * @return mixed
     */
    public function getSettings();

    /**
     * Uses _bulk to send documents to the server.
     *
     * @param array $docs
     *
     * @return mixed
     */
    public function updateDocuments(array $docs);

    /**
     * Uses _bulk to send documents to the server.
     *
     * @param array $docs
     *
     * @return mixed
     */
    public function addDocuments(array $docs);

    /**
     * Deletes entries in the db based on a query.
     *
     * @param array $query
     * @param array
     *
     * @return mixed
     */
    public function deleteByQuery($query, array $options = []);

    /**
     * Deletes the index.
     *
     * @return mixed
     */
    public function delete();

    /**
     * @param array $docs
     *
     * @return mixed
     */
    public function deleteDocuments(array $docs);

    /**
     * Optimizes search index.
     *
     * Detailed arguments can be found here in the link
     *
     * @param array $args
     *
     * @return array
     */
    public function optimize($args = []);

    /**
     * Refreshes the index.
     *
     * @return \Elastica\Response Response object
     */
    public function refresh();

    /**
     * Creates a new index with the given arguments.
     *
     * @param array      $args
     * @param bool|array $options
     *
     * @return array
     */
    public function create(array $args = [], $options = null);

    /**
     * Checks if the given index is already created.
     *
     * @return bool True if index exists
     */
    public function exists();

    /**
     * @param string|array|\Elastica\Query $query
     * @param int|array                    $options
     * @param BuilderInterface             $builder
     *
     * @return mixed
     */
    public function createSearch($query = '', $options = null, BuilderInterface $builder = null);

    /**
     * Searches in this index.
     *
     * @param string|array|\Elastica\Query $query
     * @param int|array                    $options
     *
     * @return mixed
     */
    public function search($query = '', $options = null);

    /**
     * Counts results of query.
     *
     * @param mixed $query
     *
     * @return int
     */
    public function count($query = '');

    /**
     * Opens an index.
     *
     * @return mixed
     */
    public function open();

    /**
     * Closes the index.
     *
     * @return mixed
     */
    public function close();

    /**
     * Returns the index name.
     *
     * @return string Index name
     */
    public function getName();

    /**
     * Returns index client.
     *
     * @return SearchClient
     */
    public function getClient();

    /**
     * Adds an alias to the current index.
     *
     * @param string $name
     * @param bool   $replace
     *
     * @return mixed
     */
    public function addAlias($name, $replace = false);

    /**
     * Removes an alias pointing to the current index.
     *
     * @param string $name Alias name
     *
     * @return mixed
     */
    public function removeAlias($name);

    /**
     * Returns all index aliases.
     *
     * @return array Aliases
     */
    public function getAliases();

    /**
     * Checks if the index has the given alias.
     *
     * @param string $name Alias name
     *
     * @return bool
     */
    public function hasAlias($name);

    /**
     * Clears the cache of an index.
     *
     * @return mixed
     */
    public function clearCache();

    /**
     * Flushes the index to storage.
     *
     *
     * @return mixed
     */
    public function flush();

    /**
     * Can be used to change settings during runtime.
     *
     * @param array $data Data array
     *
     * @return mixed
     */
    public function setSettings(array $data);

    /**
     * @param string $path
     * @param string $method
     * @param array  $data
     * @param array  $query
     *
     * @return mixed
     */
    public function request($path, $method, $data = [], array $query = []);

    /**
     * Analyzes a string.
     *
     *
     * @param string $text
     * @param array  $args
     *
     * @return array
     */
    public function analyze($text, $args = []);
}
