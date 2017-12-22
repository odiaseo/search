<?php

namespace MapleSyrupGroup\Search\Services\Importer\IndexMapping;

/**
 * Define non-vendor specific methods required for mapping search index.
 */
interface IndexMapping
{
    /**
     * @param array $properties Properties
     *
     * @return $this
     */
    public function setProperties(array $properties);

    /**
     * @return array $properties Properties
     */
    public function getProperties();

    /**
     * @param array $meta
     *
     * @return $this
     */
    public function setMeta(array $meta);

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param array $source
     *
     * @return $this
     */
    public function setSource(array $source);

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function disableSource($enabled = false);

    /**
     * @param string $key Key name
     * @param mixed $value Key value
     *
     * @return $this
     */
    public function setParam($key, $value);

    /**
     * @param string $key
     *
     * @return mixed $value
     */
    public function getParam($key);

    /**
     * @param array $params
     *
     * @return $this
     */
    public function setAllField(array $params);

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function enableAllField($enabled = true);

    /**
     * @param array $params
     *
     * @return $this
     */
    public function setTtl(array $params);

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function enableTtl($enabled = true);

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setParent($type);

    /**
     * @return array
     */
    public function toArray();

    /**
     * Submits the mapping and sends it to the server.
     *
     * @param array $query
     *
     * @return mixed
     */
    public function send(array $query = []);

    /**
     * Creates a mapping object.
     *
     * @param array|\Elastica\Type\Mapping $mapping Mapping object or properties array
     *
     * @throws \Elastica\Exception\InvalidException If invalid type
     *
     * @return self
     */
    public static function create($mapping);
}
