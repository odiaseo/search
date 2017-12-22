<?php

namespace MapleSyrupGroup\Search\Services\Importer\Types;

use MapleSyrupGroup\Search\Services\Importer\IndexMapping\ElasticaIndexMapping;
use MapleSyrupGroup\Search\Services\IndexBuilder\Index\Index;
use Psr\Log\LoggerInterface;

/**
 * Build a type from a predefined type (not one derived from a class).
 */
class StaticTypeBuilder implements TypeBuilder
{
    /**
     * @var array
     */
    private $typeConfiguration;

    /**
     * @var string
     */
    private $typeName;

    /**
     * @var ElasticaIndexMapping
     */
    private $mapping;

    /**
     * TypeBuilder constructor.
     *
     * @param string               $typeName
     * @param array                $typeConfiguration
     * @param ElasticaIndexMapping $mapping
     */
    public function __construct($typeName, array $typeConfiguration, ElasticaIndexMapping $mapping)
    {
        $this->setTypeConfiguration($typeConfiguration);
        $this->setTypeName($typeName);
        $this->setMapping($mapping);
    }

    /**
     * Build the type in the index.
     *
     * @param Index           $index
     * @param LoggerInterface $output
     */
    public function build(Index $index, LoggerInterface $output)
    {
        $typeName = $this->getTypeName();
        $type     = $index->getType($typeName);

        $output->info("Building type '{$typeName}'...");
        $this->getMapping()
            ->setProperties($this->getTypeConfiguration())
            ->setType($type)
            ->send();
    }

    /**
     * @return array
     */
    public function getTypeConfiguration()
    {
        return $this->typeConfiguration;
    }

    /**
     * @param array $typeConfiguration
     *
     * @return StaticTypeBuilder
     */
    public function setTypeConfiguration($typeConfiguration)
    {
        $this->typeConfiguration = $typeConfiguration;

        return $this;
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
     * @return StaticTypeBuilder
     */
    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;

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
     * @param $mapping
     *
     * @return StaticTypeBuilder
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;

        return $this;
    }
}
