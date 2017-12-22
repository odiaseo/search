<?php

namespace MapleSyrupGroup\Search\Models;

/**
 * A model we can search on.
 *
 * Contains the mappings for the object, also a way to retrieve it from its canonical data store.
 *
 * @package MapleSyrupGroup\Search\Models
 */
interface SearchableModel
{
    /**
     * @param mixed $entity
     * @return array
     */
    public function toDocumentArray($entity);

    /**
     * @return array
     */
    public function getMappingProperties();

    /**
     * @param null|int $page
     * @param null|int $pageSize
     * @return array
     */
    public function all($page = null, $pageSize = null);
}
