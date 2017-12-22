<?php

namespace MapleSyrupGroup\Search\Providers;

use Elastica\Connection;
use MapleSyrupGroup\Search\Test\Application;

class ElasticSearchClientProviderTest extends \PHPUnit_Framework_TestCase
{
    use Application;

    public function setUp()
    {
        $this->initApplication();
    }

    /**
     * @dataProvider clusterConfigDataProvider
     */
    public function testThatClusterNoteParametersAreProcessed($configString, $expectedArray)
    {
        $provider = new ElasticSearchClientProvider($this->app);
        $provider->register();

        $data = $provider->processClusterNodeParameters($configString);

        $this->assertInternalType('array', $data);

        $this->assertSame($expectedArray, $data);
    }

    public function clusterConfigDataProvider()
    {
        return [
            ['localhost:8384', [['host' => 'localhost', 'port' => 8384]]],
            ['localhost:', [['host' => 'localhost', 'port' => Connection::DEFAULT_PORT]]],
            ['127.0.0.1', [['host' => '127.0.0.1', 'port' => Connection::DEFAULT_PORT]]],
        ];
    }
}
