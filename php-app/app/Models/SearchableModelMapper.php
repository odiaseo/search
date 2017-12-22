<?php

namespace MapleSyrupGroup\Search\Models;

/**
 * Contains the mappings for the object.
 *
 * @package MapleSyrupGroup\Search\Models
 */
interface SearchableModelMapper
{
    /**
     * @return array
     */
    public function getMappingProperties();

    /**
     * Get the language used in analyzing index fields
     *
     * @return string
     */
    public function getLanguage();
}
