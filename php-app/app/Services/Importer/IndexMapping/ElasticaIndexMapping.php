<?php

namespace MapleSyrupGroup\Search\Services\Importer\IndexMapping;

use Elastica\Type;

/**
 * Vendor dependent method for mapping search index.
 */
interface ElasticaIndexMapping extends IndexMapping
{
    /**
     * @param Type $type
     *
     * @return $this
     */
    public function setType(Type $type);
}
