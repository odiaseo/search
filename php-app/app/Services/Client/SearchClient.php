<?php

namespace MapleSyrupGroup\Search\Services\Client;

/**
 * Interface SearchClient.
 */
interface SearchClient
{
    /**
     * @param array $config
     *
     * @return $this
     */
    public function setConfig(array $config);

    /**
     * Returns the index for the given connection.
     *
     * @param string $name
     *
     * @return mixed Index
     */
    public function getIndex($name);

    /**
     * Uses bulk to send documents to the server.
     *
     * @param array
     *
     * @return mixed
     */
    public function updateDocuments(array $docs);

    /**
     * @param array
     *
     * @return mixed
     */
    public function addDocuments(array $docs);

    /**
     * Update document.
     *
     * @param int    $identifier
     * @param array  $data
     * @param string $index
     * @param string $type
     * @param array  $options
     *
     * @return mixed
     */
    public function updateDocument($identifier, $data, $index, $type, array $options = []);

    /**
     * Bulk deletes documents.
     *
     * @param array $docs
     *
     * @return mixed
     */
    public function deleteDocuments(array $docs);

    /**
     * Returns the status object for all indices.
     *
     * @return mixed
     */
    public function getStatus();

    /**
     * Makes calls to the search server based on this index.
     *
     *
     * @param string
     * @param string $method Rest method to use (GET, POST, DELETE, PUT)
     * @param array  $data
     * @param array  $query
     *
     * @return mixed
     */
    public function request($path, $method = 'GET', $data = [], array $query = []);
}
