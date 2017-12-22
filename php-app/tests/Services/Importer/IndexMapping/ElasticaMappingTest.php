<?php

namespace MapleSyrupGroup\Search\Services\Importer;

use MapleSyrupGroup\Search\Services\Importer\IndexMapping\ElasticaMapping;
use Prophecy\Argument;

class ElasticaMappingTest extends \PHPUnit_Framework_TestCase
{

    public function testMappingSetsTtl()
    {
        $mapping = new ElasticaMapping();
        $mapping->enableTtl(true);

        $ttl = $mapping->getParam('_ttl');
        $this->assertTrue($ttl['enabled']);
    }
}