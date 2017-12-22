<?php

namespace MapleSyrupGroup\Search\Services\Importer\Types;

use MapleSyrupGroup\Search\Models\SearchableModel;
use MapleSyrupGroup\Search\Services\Importer\IndexMapping\ElasticaIndexMapping;
use MapleSyrupGroup\Search\Services\IndexBuilder\Index\Index;
use Psr\Log\LoggerInterface;

/**
 * Build the mapping for elastic search models that are defined in classes.
 */
class ClassTypeBuilder implements TypeBuilder
{
    /**
     * @var string
     */
    private $typeName;

    /**
     * @var SearchableModel
     */
    private $model;

    /**
     * @var ElasticaIndexMapping
     */
    private $mapping;

    /**
     * ClassTypeBuilder constructor.
     *
     * @param string $typeName
     * @param SearchableModel $model
     * @param ElasticaIndexMapping $mapping
     */
    public function __construct($typeName, SearchableModel $model, ElasticaIndexMapping $mapping)
    {
        $this->setTypeName($typeName);
        $this->setModel($model);
        $this->setMapping($mapping);
    }

    /**
     * Populate the mapping in the index provided.
     *
     * @param Index $index
     * @param LoggerInterface $output
     */
    public function build(Index $index, LoggerInterface $output)
    {
        $type = $index->getType($this->getTypeName());

        $output->info("Building type '{$this->getTypeName()}'");
        $this->getMapping()
            ->setProperties($this->getModel()->getMappingProperties())
            ->setType($type)
            ->send();
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * @param string $typeName
     *
     * @return ClassTypeBuilder
     */
    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;

        return $this;
    }

    /**
     * @return SearchableModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param SearchableModel $model
     *
     * @return ClassTypeBuilder
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return ElasticaIndexMapping
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @param ElasticaIndexMapping $mapping
     *
     * @return ClassTypeBuilder
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;

        return $this;
    }
}
