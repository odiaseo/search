<?php

namespace MapleSyrupGroup\Search\Models\Merchants;

use MapleSyrupGroup\Search\Models\Merchants\Mapper\English;
use MapleSyrupGroup\Search\Models\Merchants\Mapper\French;

class SearchableMerchantModelMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group model
     */
    public function testEnglishSearchMappingInformationIsExposed()
    {
        $nestedKeys = [
            'id',
            'is_in_store',
            'name',
            'type',
            'url_name',
        ];

        $expectedMappingKeys = [
            'name',
            'description',
            'keywords',
            'categories',
            'sales_value',
            'clicks_value',
            'id',
            'become_id',
            'last_updated',
            'related',
            'similar',
            'is_in_store',
        ];

        $mapper     = new English();
        $properties = $mapper->getMappingProperties();
        $this->assertSame('english', $mapper->getLanguage());

        foreach ($expectedMappingKeys as $key) {
            $this->assertArrayHasKey($key, $properties);
        }

        foreach ($nestedKeys as $key) {
            foreach (['related', 'similar'] as $type) {
                $this->assertArrayHasKey($key, $properties[$type]['properties']);
            }
        }
    }

    /**
     * @group model
     */
    public function testFrenchSearchMappingInformationIsExposed()
    {
        $expectedMappingKeys = [
            'id',
            'domain_id',
            'external_references',
            'name',
            'status',
            'url_name',
            'description',
            'live',
            'gambling',
            'adult',
            'clicks_value',
            'categories',
            'keywords',
            'images',
            'best_rates',
            'similar',
            'related',
            'offers',
        ];
        $mapper              = new French();
        $properties          = $mapper->getMappingProperties();
        $this->assertSame('french', $mapper->getLanguage());

        foreach ($expectedMappingKeys as $key) {
            $this->assertArrayHasKey($key, $properties);
        }
    }
}
