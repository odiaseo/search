<?php

namespace MapleSyrupGroup\Search\Services\Importer\IndexMapping;

use Elastica\Type\Mapping;

class ElasticaMapping extends Mapping implements ElasticaIndexMapping
{
    /**
     * Set TTL.
     *
     * @param array $params TTL Params (enabled, default, ...)
     *
     * @return $this
     */
    public function setTtl(array $params)
    {
        return $this->setParam('_ttl', $params);
    }

    /**
     * Enables TTL for all documents in this type.
     *
     * @param bool $enabled OPTIONAL (default = true)
     *
     * @return $this
     */
    public function enableTtl($enabled = true)
    {
        return $this->setTtl(['enabled' => $enabled]);
    }
}
